<?php
namespace App\Helpers;

class Validator
{
    private array $errors = [];

    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];
        foreach ($rules as $field => $fieldRules) {
            $fieldRules = is_array($fieldRules) ? $fieldRules : explode('|', $fieldRules);
            foreach ($fieldRules as $rule) {
                $params = [];
                if (str_contains($rule, ':')) {
                    $parts = explode(':', $rule);
                    $rule = $parts[0];
                    $params = explode(',', $parts[1]);
                }
                $value = $data[$field] ?? null;
                $ruleMethod = 'rule' . ucfirst($rule);
                if (method_exists($this, $ruleMethod)) {
                    $this->$ruleMethod($field, $value, $params);
                }
            }
        }
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): string
    {
        $first = reset($this->errors);
        return $first[0] ?? '';
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    private function ruleRequired(string $field, $value, array $params): void
    {
        if ($value === null || $value === '' || (is_array($value) && empty($value))) {
            $this->addError($field, "Le champ {$field} est requis.");
        }
    }

    private function ruleEmail(string $field, $value, array $params): void
    {
        if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, "Le champ {$field} doit être un email valide.");
        }
    }

    private function ruleMin(string $field, $value, array $params): void
    {
        $min = (int)($params[0] ?? 0);
        if ($value !== null && strlen((string)$value) < $min) {
            $this->addError($field, "Le champ {$field} doit contenir au moins {$min} caractères.");
        }
    }

    private function ruleMax(string $field, $value, array $params): void
    {
        $max = (int)($params[0] ?? 0);
        if ($value !== null && strlen((string)$value) > $max) {
            $this->addError($field, "Le champ {$field} ne peut pas dépasser {$max} caractères.");
        }
    }

    private function ruleNumeric(string $field, $value, array $params): void
    {
        if ($value !== null && $value !== '' && !is_numeric($value)) {
            $this->addError($field, "Le champ {$field} doit être un nombre.");
        }
    }

    private function ruleIn(string $field, $value, array $params): void
    {
        if ($value !== null && $value !== '' && !in_array((string)$value, $params)) {
            $this->addError($field, "Le champ {$field} n'est pas valide.");
        }
    }
}