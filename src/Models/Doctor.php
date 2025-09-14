<?php

namespace App\Models;

use App\Core\Database\Model;

class Doctor extends Model
{
    protected string $table = 'doctors';
    protected array $fillable = ['name', 'qualifications', 'workplace', 'phone', 'email', 'address'];
    protected array $guarded = ['id', 'created_at', 'updated_at'];

    public function search(string $searchTerm, int $limit = 50, int $offset = 0): array
    {
        $query = "SELECT * FROM {$this->table} 
                  WHERE name LIKE ? OR qualifications LIKE ? OR workplace LIKE ? 
                  ORDER BY name ASC";
        
        if ($limit > 0) {
            $query .= " LIMIT ? OFFSET ?";
        }

        $searchPattern = "%{$searchTerm}%";
        $params = [$searchPattern, $searchPattern, $searchPattern];
        
        if ($limit > 0) {
            $params[] = $limit;
            $params[] = $offset;
        }

        return $this->executeQuery($query, $params)->fetchAll();
    }

    public function getStatistics(): array
    {
        $query = "SELECT 
                    COUNT(*) as total_doctors,
                    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as new_this_month
                  FROM {$this->table}";
        
        return $this->executeQuery($query)->fetch();
    }

    public function getDoctorsByWorkplace(): array
    {
        $query = "SELECT workplace, COUNT(*) as count 
                  FROM {$this->table} 
                  GROUP BY workplace 
                  ORDER BY count DESC";
        
        return $this->executeQuery($query)->fetchAll();
    }
}
