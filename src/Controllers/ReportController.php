<?php

namespace App\Controllers;

use App\Models\TestReport;
use App\Models\Invoice;
use App\Models\Test;
use App\Services\ReportService;

class ReportController extends BaseController
{
    private ReportService $reportService;
    private TestReport $reportModel;
    private Invoice $invoiceModel;
    private Test $testModel;

    public function __construct()
    {
        parent::__construct();
        $this->reportModel = new TestReport();
        $this->invoiceModel = new Invoice();
        $this->testModel = new Test();
        $this->reportService = new ReportService($this->reportModel, $this->invoiceModel, $this->testModel);
    }

    public function index(): void
    {
        $this->requireAuth();

        $status = $this->getInput('status', '');
        $searchTerm = $this->getInput('search', '');

        if ($status) {
            $reports = $this->reportService->getReportsByStatus($status);
        } elseif ($status === 'pending') {
            $reports = $this->reportService->getPendingReports();
        } else {
            // Get all reports with patient and test info
            $query = "SELECT tr.*, i.patient_name, ti.test_name, d.name as doctor_name
                      FROM test_reports tr
                      JOIN invoices i ON tr.invoice_id = i.id
                      JOIN tests_info ti ON tr.test_code = ti.test_code
                      LEFT JOIN doctors d ON i.doctor_id = d.id
                      ORDER BY tr.created_at DESC
                      LIMIT 50";
            $reports = $this->reportModel->executeQuery($query)->fetchAll();
        }

        $this->view('reports/index', [
            'reports' => $reports,
            'status' => $status,
            'search' => $searchTerm
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->store();
            return;
        }

        $invoiceId = $this->getInput('invoice_id');
        $testCode = $this->getInput('test_code');

        if ($invoiceId && $testCode) {
            $invoice = $this->invoiceModel->getInvoiceWithTests($invoiceId);
            $test = $this->testModel->find($testCode);
            $parameters = $this->testModel->getTestParameters($testCode);

            $this->view('reports/create', [
                'invoice' => $invoice,
                'test' => $test,
                'parameters' => $parameters
            ]);
        } else {
            // Show form to select invoice and test
            $this->view('reports/select');
        }
    }

    public function store(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'message' => 'Invalid security token'], 400);
            return;
        }

        $data = [
            'invoice_id' => (int) $this->getInput('invoice_id'),
            'test_code' => $this->getInput('test_code'),
            'technician_id' => $this->session->getUserId(),
            'notes' => $this->sanitizeInput($this->getInput('notes', '')),
            'results' => $this->getInput('results', [])
        ];

        $result = $this->reportService->createReport($data);

        if ($result['success']) {
            $this->redirect('/NPL/report/' . $result['report_id']);
        } else {
            $this->json($result, 400);
        }
    }

    public function show(int $id): void
    {
        $this->requireAuth();

        $report = $this->reportService->getReportDetails($id);
        
        if (!$report) {
            http_response_code(404);
            echo "Report not found";
            return;
        }

        $this->view('reports/show', ['report' => $report]);
    }

    public function verify(int $id): void
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/NPL/report/' . $id);
            return;
        }

        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'message' => 'Invalid security token'], 400);
            return;
        }

        $result = $this->reportService->verifyReport($id, $this->session->getUserId());
        $this->json($result);
    }
}
