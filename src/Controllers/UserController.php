<?php

namespace App\Controllers;

use App\Models\User;
use App\Core\Validation\Validator;

class UserController extends BaseController
{
    private User $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    public function index(): void
    {
        $this->requireRole('admin');

        $searchTerm = $this->getInput('search', '');
        $role = $this->getInput('role', '');

        if ($searchTerm) {
            $query = "SELECT * FROM users WHERE username LIKE ? OR full_name LIKE ? ORDER BY username ASC";
            $searchPattern = "%{$searchTerm}%";
            $users = $this->userModel->executeQuery($query, [$searchPattern, $searchPattern])->fetchAll();
        } elseif ($role) {
            $users = $this->userModel->getUsersByRole($role);
        } else {
            $users = $this->userModel->all([], 'username ASC');
        }

        $this->view('admin/users/index', [
            'users' => $users,
            'search' => $searchTerm,
            'selectedRole' => $role
        ]);
    }

    public function create(): void
    {
        $this->requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->store();
            return;
        }

        $this->view('admin/users/create');
    }

    public function store(): void
    {
        $this->requireRole('admin');

        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'message' => 'Invalid security token'], 400);
            return;
        }

        $data = [
            'username' => $this->sanitizeInput($this->getInput('username')),
            'password' => $this->getInput('password'),
            'role' => $this->getInput('role'),
            'email' => $this->sanitizeInput($this->getInput('email', '')),
            'full_name' => $this->sanitizeInput($this->getInput('full_name', ''))
        ];

        $validator = Validator::make($data, [
            'username' => 'required|min:3|alphanumeric',
            'password' => 'required|min:6',
            'role' => 'required',
            'email' => 'email'
        ]);

        if (!$validator->validate()) {
            $this->json(['success' => false, 'errors' => $validator->getErrors()], 400);
            return;
        }

        // Check if username already exists
        $existingUser = $this->userModel->findBy('username', $data['username']);
        if ($existingUser) {
            $this->json(['success' => false, 'message' => 'Username already exists'], 400);
            return;
        }

        try {
            $this->userModel->createUser($data);
            $this->redirect('/NPL/admin/users?success=User created successfully');
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to create user: ' . $e->getMessage()], 500);
        }
    }

    public function edit(int $id): void
    {
        $this->requireRole('admin');

        $user = $this->userModel->find($id);
        if (!$user) {
            http_response_code(404);
            echo "User not found";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->update($id);
            return;
        }

        $this->view('admin/users/edit', ['user' => $user]);
    }

    public function update(int $id): void
    {
        $this->requireRole('admin');

        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'message' => 'Invalid security token'], 400);
            return;
        }

        $data = [
            'role' => $this->getInput('role'),
            'email' => $this->sanitizeInput($this->getInput('email', '')),
            'full_name' => $this->sanitizeInput($this->getInput('full_name', ''))
        ];

        // Only update password if provided
        $newPassword = $this->getInput('password');
        if (!empty($newPassword)) {
            $data['password'] = $newPassword;
        }

        $validator = Validator::make($data, [
            'role' => 'required',
            'email' => 'email'
        ]);

        if (!$validator->validate()) {
            $this->json(['success' => false, 'errors' => $validator->getErrors()], 400);
            return;
        }

        try {
            if (isset($data['password'])) {
                $this->userModel->updatePassword($id, $data['password']);
                unset($data['password']);
            }
            
            $this->userModel->update($id, $data);
            $this->redirect('/NPL/admin/users?success=User updated successfully');
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to update user: ' . $e->getMessage()], 500);
        }
    }
}
