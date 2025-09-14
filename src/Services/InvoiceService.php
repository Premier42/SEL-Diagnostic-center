<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Test;
use App\Models\Doctor;
use App\Core\Validation\Validator;

class InvoiceService
{
    private Invoice $invoiceModel;
    private Test $testModel;
    private Doctor $doctorModel;

    public function __construct(Invoice $invoiceModel, Test $testModel, Doctor $doctorModel)
    {
        $this->invoiceModel = $invoiceModel;
        $this->testModel = $testModel;
        $this->doctorModel = $doctorModel;
    }

    public function createInvoice(array $data): array
    {
        $validator = $this->validateInvoiceData($data);
        
        if (!$validator->validate()) {
            return ['success' => false, 'errors' => $validator->getErrors()];
        }

        try {
            // Calculate total amount
            $totalAmount = 0;
            $validTests = [];
            
            if (isset($data['tests']) && is_array($data['tests'])) {
                foreach ($data['tests'] as $testCode) {
                    $test = $this->testModel->find($testCode);
                    if ($test) {
                        $validTests[] = [
                            'test_code' => $testCode,
                            'price' => $test['price']
                        ];
                        $totalAmount += $test['price'];
                    }
                }
            }

            // Apply discount
            $discountAmount = $data['discount_amount'] ?? 0;
            $finalAmount = $totalAmount - $discountAmount;

            $invoiceData = [
                'patient_name' => $data['patient_name'],
                'patient_age' => $data['patient_age'],
                'patient_gender' => $data['patient_gender'],
                'patient_phone' => $data['patient_phone'] ?? '',
                'doctor_id' => $data['doctor_id'],
                'total_amount' => $finalAmount,
                'amount_paid' => $data['amount_paid'] ?? 0,
                'discount_amount' => $discountAmount,
                'payment_status' => $this->determinePaymentStatus($finalAmount, $data['amount_paid'] ?? 0),
                'notes' => $data['notes'] ?? ''
            ];

            $invoiceId = $this->invoiceModel->createInvoiceWithTests($invoiceData, $validTests);
            
            return ['success' => true, 'invoice_id' => $invoiceId];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Failed to create invoice: ' . $e->getMessage()];
        }
    }

    public function updatePayment(int $invoiceId, float $amountPaid): array
    {
        try {
            $invoice = $this->invoiceModel->find($invoiceId);
            if (!$invoice) {
                return ['success' => false, 'message' => 'Invoice not found'];
            }

            $paymentStatus = $this->determinePaymentStatus($invoice['total_amount'], $amountPaid);
            
            $success = $this->invoiceModel->updatePayment($invoiceId, $amountPaid, $paymentStatus);
            
            return ['success' => $success];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Failed to update payment: ' . $e->getMessage()];
        }
    }

    public function getInvoiceDetails(int $invoiceId): ?array
    {
        return $this->invoiceModel->getInvoiceWithTests($invoiceId);
    }

    public function searchInvoices(string $searchTerm, int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        return $this->invoiceModel->searchInvoices($searchTerm, $perPage, $offset);
    }

    public function getDashboardStats(): array
    {
        return $this->invoiceModel->getTodayStats();
    }

    private function validateInvoiceData(array $data): Validator
    {
        return Validator::make($data, [
            'patient_name' => 'required|min:2',
            'patient_age' => 'required|integer',
            'patient_gender' => 'required',
            'doctor_id' => 'required|integer',
            'tests' => 'required'
        ]);
    }

    private function determinePaymentStatus(float $totalAmount, float $amountPaid): string
    {
        if ($amountPaid >= $totalAmount) {
            return 'paid';
        } elseif ($amountPaid > 0) {
            return 'partial';
        } else {
            return 'pending';
        }
    }
}
