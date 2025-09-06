<?php

namespace App\Controllers;

use App\Models\Invoice;
use App\Models\TestReport;
use App\Models\Doctor;
use App\Models\Test;
use App\Services\InvoiceService;

class DashboardController extends BaseController
{
    private InvoiceService $invoiceService;
    private TestReport $reportModel;
    private Doctor $doctorModel;
    private Test $testModel;

    public function __construct()
    {
        parent::__construct();
        $this->invoiceService = new InvoiceService(new Invoice(), new Test(), new Doctor());
        $this->reportModel = new TestReport();
        $this->doctorModel = new Doctor();
        $this->testModel = new Test();
    }

    public function adminDashboard(): void
    {
        $this->requireRole('admin');

        $stats = $this->getAdminStats();
        $this->view('admin/dashboard', $stats);
    }

    public function staffDashboard(): void
    {
        $this->requireRole('staff');

        $stats = $this->getStaffStats();
        $this->view('staff/dashboard', $stats);
    }

    private function getAdminStats(): array
    {
        $todayStats = $this->invoiceService->getDashboardStats();
        $todayReports = $this->reportModel->getTodayReportsCount();
        $pendingReports = count($this->reportModel->getPendingReports());
        $totalDoctors = $this->doctorModel->count();
        $totalTests = $this->testModel->count();

        return [
            'today_invoices' => $todayStats['count'],
            'today_revenue' => $todayStats['total_amount'],
            'today_paid' => $todayStats['amount_paid'],
            'today_due' => $todayStats['total_due'],
            'today_reports' => $todayReports,
            'pending_reports' => $pendingReports,
            'total_doctors' => $totalDoctors,
            'total_tests' => $totalTests
        ];
    }

    private function getStaffStats(): array
    {
        $todayStats = $this->invoiceService->getDashboardStats();
        $todayReports = $this->reportModel->getTodayReportsCount();
        $pendingReports = $this->reportModel->getPendingReports();
        $recentInvoices = (new Invoice())->getRecentInvoices(5);

        return [
            'today_invoices' => $todayStats['count'],
            'today_revenue' => $todayStats['total_amount'],
            'today_paid' => $todayStats['amount_paid'],
            'today_due' => $todayStats['total_due'],
            'today_reports' => $todayReports,
            'pending_reports' => $pendingReports,
            'recent_invoices' => $recentInvoices
        ];
    }
}
