<?php

namespace App\Controllers;

class DoctorController extends BaseController
{
    public function index()
    {
        $db = getDB();

        try {
            $search = $_GET['search'] ?? '';

            if ($search) {
                $stmt = $db->prepare("
                    SELECT * FROM doctors
                    WHERE (name LIKE ? OR phone LIKE ? OR specialization LIKE ?)
                    AND is_active = 1
                    ORDER BY name ASC
                ");
                $searchTerm = "%{$search}%";
                $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
            } else {
                $stmt = $db->query("
                    SELECT * FROM doctors
                    WHERE is_active = 1
                    ORDER BY name ASC
                ");
            }

            $doctors = $stmt->fetchAll();

            include __DIR__ . '/../../views/doctors/index.php';

        } catch (Exception $e) {
            log_activity("Doctor listing error: " . $e->getMessage(), 'error');
            $error_message = config('app.debug') ? $e->getMessage() : 'Unable to load doctors.';
            include __DIR__ . '/../../views/errors/500.php';
        }
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->store();
            return;
        }

        include __DIR__ . '/../../views/doctors/create.php';
    }

    public function store()
    {
        $db = getDB();

        try {
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'qualifications' => trim($_POST['qualifications'] ?? ''),
                'specialization' => trim($_POST['specialization'] ?? ''),
                'workplace' => trim($_POST['workplace'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'license_number' => trim($_POST['license_number'] ?? '')
            ];

            // Validation
            if (empty($data['name'])) {
                $_SESSION['errors'] = ['name' => 'Name is required'];
                $_SESSION['old'] = $data;
                redirect('/doctors/create');
                return;
            }

            $stmt = $db->prepare("
                INSERT INTO doctors (name, qualifications, specialization, workplace, phone, email, address, license_number)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $data['name'],
                $data['qualifications'],
                $data['specialization'],
                $data['workplace'],
                $data['phone'],
                $data['email'],
                $data['address'],
                $data['license_number']
            ]);

            $_SESSION['success'] = 'Doctor added successfully';
            redirect('/doctors');

        } catch (Exception $e) {
            log_activity("Doctor creation error: " . $e->getMessage(), 'error');
            $_SESSION['errors'] = ['general' => 'Failed to add doctor'];
            redirect('/doctors/create');
        }
    }
}