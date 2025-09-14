<?php

namespace App\Models;

use App\Core\Database\Model;

class TestReport extends Model
{
    protected string $table = 'test_reports';
    protected array $fillable = ['invoice_id', 'test_code', 'report_data', 'status', 'technician_id', 'verified_by', 'notes'];
    protected array $guarded = ['id', 'created_at', 'updated_at'];

    public function createReportWithResults(array $reportData, array $results): int
    {
        try {
            $this->db->beginTransaction();
            
            // Create report
            $reportId = $this->create($reportData);
            
            // Add test results
            if (!empty($results)) {
                $resultQuery = "INSERT INTO test_results (report_id, parameter_name, value, unit, normal_range, is_abnormal) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $this->db->prepare($resultQuery);
                
                foreach ($results as $result) {
                    $stmt->execute([
                        $reportId,
                        $result['parameter_name'],
                        $result['value'],
                        $result['unit'],
                        $result['normal_range'],
                        $result['is_abnormal'] ?? 0
                    ]);
                }
            }
            
            $this->db->commit();
            return $reportId;
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function getReportWithResults(int $reportId): ?array
    {
        $report = $this->find($reportId);
        if (!$report) {
            return null;
        }

        // Get test results
        $resultQuery = "SELECT * FROM test_results WHERE report_id = ? ORDER BY id";
        $results = $this->executeQuery($resultQuery, [$reportId])->fetchAll();

        // Get invoice and patient info
        $invoiceQuery = "SELECT i.*, d.name as doctor_name 
                         FROM invoices i 
                         LEFT JOIN doctors d ON i.doctor_id = d.id 
                         WHERE i.id = ?";
        $invoice = $this->executeQuery($invoiceQuery, [$report['invoice_id']])->fetch();

        // Get test info
        $testQuery = "SELECT * FROM tests_info WHERE test_code = ?";
        $test = $this->executeQuery($testQuery, [$report['test_code']])->fetch();

        $report['results'] = $results;
        $report['invoice'] = $invoice;
        $report['test_info'] = $test;
        
        return $report;
    }

    public function getPendingReports(): array
    {
        $query = "SELECT tr.*, i.patient_name, ti.test_name, d.name as doctor_name
                  FROM {$this->table} tr
                  JOIN invoices i ON tr.invoice_id = i.id
                  JOIN tests_info ti ON tr.test_code = ti.test_code
                  LEFT JOIN doctors d ON i.doctor_id = d.id
                  WHERE tr.status = 'pending'
                  ORDER BY tr.created_at ASC";
        
        return $this->executeQuery($query)->fetchAll();
    }

    public function getTodayReportsCount(): int
    {
        $today = date('Y-m-d');
        $query = "SELECT COUNT(*) FROM {$this->table} WHERE DATE(created_at) = ?";
        return (int) $this->executeQuery($query, [$today])->fetchColumn();
    }

    public function getReportsByStatus(string $status): array
    {
        $query = "SELECT tr.*, i.patient_name, ti.test_name, d.name as doctor_name
                  FROM {$this->table} tr
                  JOIN invoices i ON tr.invoice_id = i.id
                  JOIN tests_info ti ON tr.test_code = ti.test_code
                  LEFT JOIN doctors d ON i.doctor_id = d.id
                  WHERE tr.status = ?
                  ORDER BY tr.created_at DESC";
        
        return $this->executeQuery($query, [$status])->fetchAll();
    }

    public function updateReportStatus(int $reportId, string $status, int $verifiedBy = null): bool
    {
        $data = ['status' => $status];
        if ($verifiedBy) {
            $data['verified_by'] = $verifiedBy;
        }
        
        return $this->update($reportId, $data);
    }
}
