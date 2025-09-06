<?php

namespace App\Models;

use App\Core\Database\Model;

class Invoice extends Model
{
    protected string $table = 'invoices';
    protected array $fillable = ['patient_name', 'patient_age', 'patient_gender', 'patient_phone', 'doctor_id', 'total_amount', 'amount_paid', 'discount_amount', 'payment_status', 'notes'];
    protected array $guarded = ['id', 'created_at', 'updated_at'];

    public function createInvoiceWithTests(array $invoiceData, array $tests): int
    {
        try {
            $this->db->beginTransaction();
            
            // Create invoice
            $invoiceId = $this->create($invoiceData);
            
            // Add tests to invoice
            if (!empty($tests)) {
                $testQuery = "INSERT INTO invoice_tests (invoice_id, test_code, price) VALUES (?, ?, ?)";
                $stmt = $this->db->prepare($testQuery);
                
                foreach ($tests as $test) {
                    $stmt->execute([$invoiceId, $test['test_code'], $test['price']]);
                }
            }
            
            $this->db->commit();
            return $invoiceId;
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function getInvoiceWithTests(int $invoiceId): ?array
    {
        $invoice = $this->find($invoiceId);
        if (!$invoice) {
            return null;
        }

        // Get tests
        $testQuery = "SELECT it.*, ti.test_name 
                      FROM invoice_tests it 
                      JOIN tests_info ti ON it.test_code = ti.test_code 
                      WHERE it.invoice_id = ?";
        $tests = $this->executeQuery($testQuery, [$invoiceId])->fetchAll();

        // Get doctor info
        $doctorQuery = "SELECT name FROM doctors WHERE id = ?";
        $doctor = $this->executeQuery($doctorQuery, [$invoice['doctor_id']])->fetch();

        $invoice['tests'] = $tests;
        $invoice['doctor_name'] = $doctor['name'] ?? 'Unknown';
        
        return $invoice;
    }

    public function getTodayStats(): array
    {
        $today = date('Y-m-d');
        $query = "SELECT 
                    COUNT(*) as count,
                    COALESCE(SUM(total_amount), 0) as total_amount,
                    COALESCE(SUM(amount_paid), 0) as amount_paid,
                    COALESCE(SUM(total_amount - amount_paid - discount_amount), 0) as total_due
                  FROM {$this->table} 
                  WHERE DATE(created_at) = ?";
        
        return $this->executeQuery($query, [$today])->fetch();
    }

    public function getRecentInvoices(int $limit = 10): array
    {
        $query = "SELECT i.*, d.name as doctor_name 
                  FROM {$this->table} i 
                  LEFT JOIN doctors d ON i.doctor_id = d.id 
                  ORDER BY i.created_at DESC 
                  LIMIT ?";
        
        return $this->executeQuery($query, [$limit])->fetchAll();
    }

    public function searchInvoices(string $searchTerm, int $limit = 50, int $offset = 0): array
    {
        $query = "SELECT i.*, d.name as doctor_name 
                  FROM {$this->table} i 
                  LEFT JOIN doctors d ON i.doctor_id = d.id 
                  WHERE i.patient_name LIKE ? OR i.patient_phone LIKE ? OR i.id = ? 
                  ORDER BY i.created_at DESC";
        
        if ($limit > 0) {
            $query .= " LIMIT ? OFFSET ?";
        }

        $searchPattern = "%{$searchTerm}%";
        $params = [$searchPattern, $searchPattern, $searchTerm];
        
        if ($limit > 0) {
            $params[] = $limit;
            $params[] = $offset;
        }

        return $this->executeQuery($query, $params)->fetchAll();
    }

    public function updatePayment(int $invoiceId, float $amountPaid, string $paymentStatus): bool
    {
        return $this->update($invoiceId, [
            'amount_paid' => $amountPaid,
            'payment_status' => $paymentStatus
        ]);
    }
}
