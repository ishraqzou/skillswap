<?php

namespace App\Core;

/**
 * Validator - Validation de formulaires côté serveur (NFR-002).
 */
class Validator
{
    private array $errors = [];
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function required(string $field, string $label): self
    {
        if (empty($this->data[$field]) && $this->data[$field] !== '0') {
            $this->errors[$field] = "Le champ « {$label} » est obligatoire.";
        }
        return $this;
    }

    public function email(string $field): self
    {
        if (!empty($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "L'adresse e-mail saisie n'est pas valide.";
        }
        return $this;
    }

    public function minLength(string $field, int $min, string $label): self
    {
        if (!empty($this->data[$field]) && mb_strlen($this->data[$field]) < $min) {
            $this->errors[$field] = "Le champ « {$label} » doit contenir au moins {$min} caractères.";
        }
        return $this;
    }

    public function matches(string $field, string $otherField, string $label): self
    {
        if (($this->data[$field] ?? null) !== ($this->data[$otherField] ?? null)) {
            $this->errors[$field] = "Le champ « {$label} » ne correspond pas.";
        }
        return $this;
    }

    public function passwordComplexity(string $field): self
    {
        $value = $this->data[$field] ?? '';
        if (strlen($value) < PASSWORD_MIN_LENGTH
            || !preg_match('/[A-Za-z]/', $value)
            || !preg_match('/[0-9]/', $value)
        ) {
            $this->errors[$field] = 'Le mot de passe doit contenir au moins ' . PASSWORD_MIN_LENGTH
                . ' caractères, avec au moins une lettre et un chiffre (BR-002).';
        }
        return $this;
    }

    public function in(string $field, array $allowed, string $label): self
    {
        if (!empty($this->data[$field]) && !in_array($this->data[$field], $allowed, true)) {
            $this->errors[$field] = "La valeur du champ « {$label} » n'est pas valide.";
        }
        return $this;
    }

    public function fails(): bool
    {
        return count($this->errors) > 0;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        return $this->errors ? array_values($this->errors)[0] : null;
    }
}
