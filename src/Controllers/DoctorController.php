<?php

namespace App\Controllers;

use App\Models\Doctor;
use App\Core\Validation\Validator;

class DoctorController extends BaseController
{
    private Doctor $doctorModel;

    public function __construct()
    {
        parent::__construct();
        $this->doctorModel = new Doctor();
    }

    public function index(): void
    {
        $this->requireRole('admin');

        $searchTerm = $this->getInput('search', '');
        $page = (int) $this->getInput('page', 1);
        $perPage = 20;

        if ($searchTerm) {
            $doctors = $this->doctorModel->search($searchTerm, $perPage, ($page - 1) * $perPage);
        } else {
            $doctors = $this->doctorModel->all([], 'name ASC', $perPage, ($page - 1) * $perPage);
        }

        $this->view('admin/doctors/index', [
            'doctors' => $doctors,
            'search' => $searchTerm,
            'page' => $page
        ]);
    }

    public function create(): void
    {
        $this->requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->store();
            return;
        }

        $this->view('admin/doctors/create');
    }

    public function store(): void
    {
        $this->requireRole('admin');

        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'message' => 'Invalid security token'], 400);
            return;
        }

        $data = [
            'name' => $this->sanitizeInput($this->getInput('name')),
            'qualifications' => $this->sanitizeInput($this->getInput('qualifications')),
            'workplace' => $this->sanitizeInput($this->getInput('workplace')),
            'phone' => $this->sanitizeInput($this->getInput('phone', '')),
            'email' => $this->sanitizeInput($this->getInput('email', '')),
            'address' => $this->sanitizeInput($this->getInput('address', ''))
        ];

        $validator = Validator::make($data, [
            'name' => 'required|min:2',
            'qualifications' => 'required',
            'workplace' => 'required',
            'email' => 'email'
        ]);

        if (!$validator->validate()) {
            $this->json(['success' => false, 'errors' => $validator->getErrors()], 400);
            return;
        }

        try {
            $this->doctorModel->create($data);
            $this->redirect('/NPL/admin/doctors?success=Doctor added successfully');
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to add doctor: ' . $e->getMessage()], 500);
        }
    }

    public function edit(int $id): void
    {
        $this->requireRole('admin');

        $doctor = $this->doctorModel->find($id);
        if (!$doctor) {
            http_response_code(404);
            echo "Doctor not found";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->update($id);
            return;
        }

        $this->view('admin/doctors/edit', ['doctor' => $doctor]);
    }

    public function update(int $id): void
    {
        $this->requireRole('admin');

        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'message' => 'Invalid security token'], 400);
            return;
        }

        $data = [
            'name' => $this->sanitizeInput($this->getInput('name')),
            'qualifications' => $this->sanitizeInput($this->getInput('qualifications')),
            'workplace' => $this->sanitizeInput($this->getInput('workplace')),
            'phone' => $this->sanitizeInput($this->getInput('phone', '')),
            'email' => $this->sanitizeInput($this->getInput('email', '')),
            'address' => $this->sanitizeInput($this->getInput('address', ''))
        ];

        $validator = Validator::make($data, [
            'name' => 'required|min:2',
            'qualifications' => 'required',
            'workplace' => 'required',
            'email' => 'email'
        ]);

        if (!$validator->validate()) {
            $this->json(['success' => false, 'errors' => $validator->getErrors()], 400);
            return;
        }

        try {
            $this->doctorModel->update($id, $data);
            $this->redirect('/NPL/admin/doctors?success=Doctor updated successfully');
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to update doctor: ' . $e->getMessage()], 500);
        }
    }
}
