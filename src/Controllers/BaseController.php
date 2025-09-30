<?php

namespace App\Controllers;

class BaseController
{
    protected function requireAuth()
    {
        if (!isset($_SESSION['user_id'])) {
            redirect('/');
            exit;
        }
    }

    protected function requireRole($role)
    {
        $this->requireAuth();

        if ($_SESSION['role'] !== $role) {
            http_response_code(403);
            include __DIR__ . '/../../views/errors/403.php';
            exit;
        }
    }

    protected function json($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function validate($data, $rules)
    {
        $errors = [];

        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            $ruleArray = explode('|', $rule);

            foreach ($ruleArray as $singleRule) {
                if ($singleRule === 'required' && empty($value)) {
                    $errors[$field][] = ucfirst($field) . ' is required';
                }

                if (strpos($singleRule, 'min:') === 0) {
                    $minLength = (int)substr($singleRule, 4);
                    if (strlen($value) < $minLength) {
                        $errors[$field][] = ucfirst($field) . " must be at least {$minLength} characters";
                    }
                }

                if (strpos($singleRule, 'max:') === 0) {
                    $maxLength = (int)substr($singleRule, 4);
                    if (strlen($value) > $maxLength) {
                        $errors[$field][] = ucfirst($field) . " must not exceed {$maxLength} characters";
                    }
                }

                if ($singleRule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = ucfirst($field) . ' must be a valid email';
                }

                if ($singleRule === 'numeric' && !is_numeric($value)) {
                    $errors[$field][] = ucfirst($field) . ' must be numeric';
                }
            }
        }

        return $errors;
    }

    protected function sanitize($input)
    {
        if (is_array($input)) {
            return array_map([$this, 'sanitize'], $input);
        }

        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    protected function flashMessage($type, $message)
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }

    protected function getFlashMessage()
    {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
}
