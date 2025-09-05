<?php

namespace App\Controllers;

use App\Models\Test;
use App\Core\Validation\Validator;

class TestController extends BaseController
{
    private Test $testModel;

    public function __construct()
    {
        parent::__construct();
        $this->testModel = new Test();
    }

    public function index(): void
    {
        $this->requireRole('admin');

        $searchTerm = $this->getInput('search', '');
        $category = $this->getInput('category', '');

        if ($searchTerm) {
            $tests = $this->testModel->searchTests($searchTerm);
        } elseif ($category) {
            $tests = $this->testModel->getTestsByCategory($category);
        } else {
            $tests = $this->testModel->all([], 'category ASC, test_name ASC');
        }

        $categories = $this->testModel->getCategories();

        $this->view('admin/tests/index', [
            'tests' => $tests,
            'categories' => $categories,
            'search' => $searchTerm,
            'selectedCategory' => $category
        ]);
    }

    public function create(): void
    {
        $this->requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->store();
            return;
        }

        $this->view('admin/tests/create');
    }

    public function store(): void
    {
        $this->requireRole('admin');

        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'message' => 'Invalid security token'], 400);
            return;
        }

        $testData = [
            'test_code' => $this->sanitizeInput($this->getInput('test_code')),
            'test_name' => $this->sanitizeInput($this->getInput('test_name')),
            'price' => (float) $this->getInput('price'),
            'category' => $this->sanitizeInput($this->getInput('category')),
            'description' => $this->sanitizeInput($this->getInput('description', ''))
        ];

        $parameters = $this->getInput('parameters', []);

        $validator = Validator::make($testData, [
            'test_code' => 'required|alphanumeric',
            'test_name' => 'required|min:2',
            'price' => 'required|numeric',
            'category' => 'required'
        ]);

        if (!$validator->validate()) {
            $this->json(['success' => false, 'errors' => $validator->getErrors()], 400);
            return;
        }

        try {
            $this->testModel->createTestWithParameters($testData, $parameters);
            $this->redirect('/NPL/admin/tests?success=Test created successfully');
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to create test: ' . $e->getMessage()], 500);
        }
    }

    public function edit(string $testCode): void
    {
        $this->requireRole('admin');

        $test = $this->testModel->find($testCode);
        if (!$test) {
            http_response_code(404);
            echo "Test not found";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->update($testCode);
            return;
        }

        $parameters = $this->testModel->getTestParameters($testCode);

        $this->view('admin/tests/edit', [
            'test' => $test,
            'parameters' => $parameters
        ]);
    }

    public function update(string $testCode): void
    {
        $this->requireRole('admin');

        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'message' => 'Invalid security token'], 400);
            return;
        }

        $testData = [
            'test_name' => $this->sanitizeInput($this->getInput('test_name')),
            'price' => (float) $this->getInput('price'),
            'category' => $this->sanitizeInput($this->getInput('category')),
            'description' => $this->sanitizeInput($this->getInput('description', ''))
        ];

        $parameters = $this->getInput('parameters', []);

        $validator = Validator::make($testData, [
            'test_name' => 'required|min:2',
            'price' => 'required|numeric',
            'category' => 'required'
        ]);

        if (!$validator->validate()) {
            $this->json(['success' => false, 'errors' => $validator->getErrors()], 400);
            return;
        }

        try {
            $this->testModel->updateTestWithParameters($testCode, $testData, $parameters);
            $this->redirect('/NPL/admin/tests?success=Test updated successfully');
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to update test: ' . $e->getMessage()], 500);
        }
    }
}
