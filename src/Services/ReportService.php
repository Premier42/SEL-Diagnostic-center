<?php

namespace App\Services;

use App\Models\TestReport;
use App\Models\Invoice;
use App\Models\Test;
use App\Core\Validation\Validator;

class ReportService
{
    private TestReport $reportModel;
    private Invoice $invoiceModel;
    private Test $testModel;

    public function __construct(TestReport $reportModel, Invoice $invoiceModel, Test $testModel)
    {
        $this->reportModel = $reportModel;
        $this->invoiceModel = $invoiceModel;
        $this->testModel = $testModel;
    }

    public function createReport(array $data): array
    {
        $validator = $this->validateReportData($data);
        
        if (!$validator->validate()) {
            return ['success' => false, 'errors' => $validator->getErrors()];
        }

        try {
            $reportData = [
                'invoice_id' => $data['invoice_id'],
                'test_code' => $data['test_code'],
                'status' => 'pending',
                'technician_id' => $data['technician_id'] ?? null,
                'notes' => $data['notes'] ?? ''
            ];

            $results = $data['results'] ?? [];
            $reportId = $this->reportModel->createReportWithResults($reportData, $results);
            
            return ['success' => true, 'report_id' => $reportId];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Failed to create report: ' . $e->getMessage()];
        }
    }

    public function updateReportResults(int $reportId, array $results): array
    {
        try {
            // Get existing results to preserve historical data
            $existingQuery = "SELECT * FROM test_results WHERE report_id = ?";
            $existingResults = $this->reportModel->executeQuery($existingQuery, [$reportId])->fetchAll();
            
            // Create backup of existing results before update
            if (!empty($existingResults)) {
                $backupQuery = "INSERT INTO test_results_history (report_id, parameter_name, value, unit, normal_range, is_abnormal, backed_up_at) 
                               SELECT report_id, parameter_name, value, unit, normal_range, is_abnormal, NOW() FROM test_results WHERE report_id = ?";
                $this->reportModel->executeQuery($backupQuery, [$reportId]);
            }

            // Mark existing results as inactive instead of deleting
            $deactivateQuery = "UPDATE test_results SET is_active = 0, updated_at = NOW() WHERE report_id = ?";
            $this->reportModel->executeQuery($deactivateQuery, [$reportId]);

            // Insert new results
            if (!empty($results)) {
                $resultQuery = "INSERT INTO test_results (report_id, parameter_name, value, unit, normal_range, is_abnormal, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, 1, NOW())";
                $stmt = $this->reportModel->db->prepare($resultQuery);
                
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

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Failed to update results: ' . $e->getMessage()];
        }
    }

    public function verifyReport(int $reportId, int $verifiedBy): array
    {
        try {
            $success = $this->reportModel->updateReportStatus($reportId, 'verified', $verifiedBy);
            return ['success' => $success];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Failed to verify report: ' . $e->getMessage()];
        }
    }

    public function getReportDetails(int $reportId): ?array
    {
        return $this->reportModel->getReportWithResults($reportId);
    }

    public function getPendingReports(): array
    {
        return $this->reportModel->getPendingReports();
    }

    public function getReportsByStatus(string $status): array
    {
        return $this->reportModel->getReportsByStatus($status);
    }

    public function generateReportPDF(int $reportId): array
    {
        try {
            $report = $this->getReportDetails($reportId);
            if (!$report) {
                return ['success' => false, 'message' => 'Report not found'];
            }

            // Here you would implement PDF generation logic
            // For now, we'll return the report data
            return ['success' => true, 'report' => $report];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Failed to generate PDF: ' . $e->getMessage()];
        }
    }

    private function validateReportData(array $data): Validator
    {
        return Validator::make($data, [
            'invoice_id' => 'required|integer',
            'test_code' => 'required',
            'results' => 'required'
        ]);
    }
}
