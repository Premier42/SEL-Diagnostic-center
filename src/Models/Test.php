<?php

namespace App\Models;

use App\Core\Database\Model;

class Test extends Model
{
    protected string $table = 'tests_info';
    protected string $primaryKey = 'test_code';
    protected array $fillable = ['test_code', 'test_name', 'price', 'category', 'description'];
    protected array $guarded = ['created_at', 'updated_at'];

    public function getTestParameters(string $testCode): array
    {
        $query = "SELECT * FROM test_parameters WHERE test_code = ? AND (is_active IS NULL OR is_active = 1) ORDER BY sort_order ASC";
        return $this->executeQuery($query, [$testCode])->fetchAll();
    }

    public function createTestWithParameters(array $testData, array $parameters): bool
    {
        try {
            $this->db->beginTransaction();
            
            // Create test
            $this->create($testData);
            
            // Create parameters with is_active flag
            if (!empty($parameters)) {
                $paramQuery = "INSERT INTO test_parameters (test_code, parameter_name, unit, normal_range, sort_order, is_active) VALUES (?, ?, ?, ?, ?, 1)";
                $stmt = $this->db->prepare($paramQuery);
                
                foreach ($parameters as $index => $param) {
                    $stmt->execute([
                        $testData['test_code'],
                        $param['name'],
                        $param['unit'],
                        $param['range'],
                        $index + 1
                    ]);
                }
            }
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function updateTestWithParameters(string $testCode, array $testData, array $parameters): bool
    {
        try {
            $this->db->beginTransaction();
            
            // Update test info only
            $this->update($testCode, $testData);
            
            // Get existing parameters to preserve data
            $existingParams = $this->getTestParameters($testCode);
            $existingParamMap = [];
            foreach ($existingParams as $param) {
                $existingParamMap[$param['parameter_name']] = $param;
            }
            
            // Update or insert parameters without deleting existing ones
            if (!empty($parameters)) {
                foreach ($parameters as $index => $param) {
                    $paramName = $param['name'];
                    
                    if (isset($existingParamMap[$paramName])) {
                        // Update existing parameter
                        $updateQuery = "UPDATE test_parameters SET unit = ?, normal_range = ?, sort_order = ? WHERE test_code = ? AND parameter_name = ?";
                        $this->executeQuery($updateQuery, [
                            $param['unit'],
                            $param['range'],
                            $index + 1,
                            $testCode,
                            $paramName
                        ]);
                        unset($existingParamMap[$paramName]);
                    } else {
                        // Insert new parameter
                        $insertQuery = "INSERT INTO test_parameters (test_code, parameter_name, unit, normal_range, sort_order) VALUES (?, ?, ?, ?, ?)";
                        $this->executeQuery($insertQuery, [
                            $testCode,
                            $paramName,
                            $param['unit'],
                            $param['range'],
                            $index + 1
                        ]);
                    }
                }
            }
            
            // Mark unused parameters as inactive instead of deleting
            foreach ($existingParamMap as $unusedParam) {
                $deactivateQuery = "UPDATE test_parameters SET is_active = 0 WHERE test_code = ? AND parameter_name = ?";
                $this->executeQuery($deactivateQuery, [$testCode, $unusedParam['parameter_name']]);
            }
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function getTestsByCategory(string $category = null): array
    {
        if ($category) {
            return $this->all(['category' => $category], 'test_name ASC');
        }
        return $this->all([], 'category ASC, test_name ASC');
    }

    public function getCategories(): array
    {
        $query = "SELECT DISTINCT category FROM {$this->table} WHERE category IS NOT NULL ORDER BY category";
        return $this->executeQuery($query)->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function searchTests(string $searchTerm): array
    {
        $query = "SELECT * FROM {$this->table} 
                  WHERE test_name LIKE ? OR test_code LIKE ? OR category LIKE ? 
                  ORDER BY test_name ASC";
        
        $searchPattern = "%{$searchTerm}%";
        return $this->executeQuery($query, [$searchPattern, $searchPattern, $searchPattern])->fetchAll();
    }
}
