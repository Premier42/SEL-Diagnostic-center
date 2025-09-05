<?php

namespace App\Controllers;

use App\Models\Invoice;
use App\Models\Test;
use App\Models\Doctor;
use App\Services\InvoiceService;

class InvoiceController extends BaseController
{
    private InvoiceService $invoiceService;
    private Doctor $doctorModel;
    private Test $testModel;

    public function __construct()
    {
        parent::__construct();
        $this->invoiceService = new InvoiceService(new Invoice(), new Test(), new Doctor());
        $this->doctorModel = new Doctor();
        $this->testModel = new Test();
    }

    public function index(): void
    {
        $this->requireAuth();
        
        $searchTerm = $this->getInput('search', '');
        $page = (int) $this->getInput('page', 1);
        
        if ($searchTerm) {
            $invoices = $this->invoiceService->searchInvoices($searchTerm, $page);
        } else {
            $invoices = (new Invoice())->getRecentInvoices(20);
        }

        $this->view('invoice/index', [
            'invoices' => $invoices,
            'search' => $searchTerm,
            'page' => $page
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->store();
            return;
        }

        $doctors = $this->doctorModel->all([], 'name ASC');
        $tests = $this->testModel->all([], 'test_name ASC');

        $this->view('invoice/create', [
            'doctors' => $doctors,
            'tests' => $tests
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'message' => 'Invalid security token'], 400);
            return;
        }

        $data = [
            'patient_name' => $this->sanitizeInput($this->getInput('patient_name')),
            'patient_age' => (int) $this->getInput('patient_age'),
            'patient_gender' => $this->getInput('patient_gender'),
            'patient_phone' => $this->sanitizeInput($this->getInput('patient_phone')),
            'doctor_id' => (int) $this->getInput('doctor_id'),
            'tests' => $this->getInput('tests', []),
            'discount_amount' => (float) $this->getInput('discount_amount', 0),
            'amount_paid' => (float) $this->getInput('amount_paid', 0),
            'notes' => $this->sanitizeInput($this->getInput('notes', ''))
        ];

        $result = $this->invoiceService->createInvoice($data);

        if ($result['success']) {
            $this->redirect('/invoice/' . $result['invoice_id']);
        } else {
            $this->json($result, 400);
        }
    }

    public function show(int $id): void
    {
        $this->requireAuth();

        $invoice = $this->invoiceService->getInvoiceDetails($id);
        
        if (!$invoice) {
            http_response_code(404);
            echo "Invoice not found";
            return;
        }

        $this->view('invoice/show', ['invoice' => $invoice]);
    }

    public function updatePayment(int $id): void
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/invoice/' . $id);
            return;
        }

        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'message' => 'Invalid security token'], 400);
            return;
        }

        $amountPaid = (float) $this->getInput('amount_paid', 0);
        $result = $this->invoiceService->updatePayment($id, $amountPaid);

        $this->json($result);
    }
}
