<?php
namespace Core;

class Validator {
    private array $errors = [];

    /**
     * Validates data against a set of rules.
     * Example: ['username' => ['required', 'min:3'], 'email' => ['required', 'email']]
     */
    public function validate(array $data, array $rules): bool {
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;

            foreach ($fieldRules as $rule) {
                if ($rule === 'required' && empty($value)) {
                    $this->addError($field, "The {$field} field is required.");
                }

                if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "The {$field} must be a valid email address.");
                }

                if (str_starts_with($rule, 'min:')) {
                    $min = (int) substr($rule, 4);
                    if (strlen($value) < $min) {
                        $this->addError($field, "The {$field} must be at least {$min} characters.");
                    }
                }
            }
        }

        return empty($this->errors);
    }

    private function addError(string $field, string $message): void {
        $this->errors[$field][] = $message;
    }

    public function getErrors(): array {
        return $this->errors;
    }
}