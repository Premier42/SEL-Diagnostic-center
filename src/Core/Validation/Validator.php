<?php

namespace App\Core\Validation;

class Validator
{
    private array $data;
    private array $rules;
    private array $errors = [];
    private array $customMessages = [];

    public function __construct(array $data, array $rules, array $customMessages = [])
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->customMessages = $customMessages;
    }

    public function validate(): bool
    {
        foreach ($this->rules as $field => $rules) {
            $fieldRules = is_string($rules) ? explode('|', $rules) : $rules;
            $this->validateField($field, $fieldRules);
        }

        return empty($this->errors);
    }

    private function validateField(string $field, array $rules): void
    {
        $value = $this->data[$field] ?? null;

        foreach ($rules as $rule) {
            $this->applyRule($field, $value, $rule);
        }
    }

    private function applyRule(string $field, $value, string $rule): void
    {
        $parts = explode(':', $rule, 2);
        $ruleName = $parts[0];
        $parameter = $parts[1] ?? null;

        switch ($ruleName) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    $this->addError($field, 'required');
                }
                break;

            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, 'email');
                }
                break;

            case 'min':
                if (!empty($value) && strlen($value) < (int)$parameter) {
                    $this->addError($field, 'min', ['min' => $parameter]);
                }
                break;

            case 'max':
                if (!empty($value) && strlen($value) > (int)$parameter) {
                    $this->addError($field, 'max', ['max' => $parameter]);
                }
                break;

            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->addError($field, 'numeric');
                }
                break;

            case 'integer':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_INT)) {
                    $this->addError($field, 'integer');
                }
                break;

            case 'alpha':
                if (!empty($value) && !ctype_alpha($value)) {
                    $this->addError($field, 'alpha');
                }
                break;

            case 'alphanumeric':
                if (!empty($value) && !ctype_alnum($value)) {
                    $this->addError($field, 'alphanumeric');
                }
                break;

            case 'date':
                if (!empty($value) && !strtotime($value)) {
                    $this->addError($field, 'date');
                }
                break;

            case 'unique':
                // This would require database validation
                // Implementation depends on specific requirements
                break;
        }
    }

    private function addError(string $field, string $rule, array $parameters = []): void
    {
        $message = $this->getErrorMessage($field, $rule, $parameters);
        $this->errors[$field][] = $message;
    }

    private function getErrorMessage(string $field, string $rule, array $parameters = []): string
    {
        $key = "{$field}.{$rule}";
        
        if (isset($this->customMessages[$key])) {
            return $this->customMessages[$key];
        }

        $defaultMessages = [
            'required' => "The {$field} field is required.",
            'email' => "The {$field} must be a valid email address.",
            'min' => "The {$field} must be at least {$parameters['min']} characters.",
            'max' => "The {$field} may not be greater than {$parameters['max']} characters.",
            'numeric' => "The {$field} must be a number.",
            'integer' => "The {$field} must be an integer.",
            'alpha' => "The {$field} may only contain letters.",
            'alphanumeric' => "The {$field} may only contain letters and numbers.",
            'date' => "The {$field} is not a valid date.",
            'unique' => "The {$field} has already been taken."
        ];

        return $defaultMessages[$rule] ?? "The {$field} field is invalid.";
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getFirstError(string $field = null): ?string
    {
        if ($field) {
            return $this->errors[$field][0] ?? null;
        }

        foreach ($this->errors as $fieldErrors) {
            return $fieldErrors[0] ?? null;
        }

        return null;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public static function make(array $data, array $rules, array $customMessages = []): self
    {
        return new self($data, $rules, $customMessages);
    }
}
