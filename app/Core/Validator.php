<?php

declare(strict_types=1);

namespace App\Core;

use App\Exceptions\ValidationException;

/**
 * Validates form data with chainable rules.
 *
 * Example:
 *   (new Validator($data))->required('email', 'Email')->email('email', 'Email')->validate();
 */
final class Validator
{
    /** @var array<string, string> */
    private array $errors = [];

    public function __construct(private array $data)
    {
    }

    public function required(string $field, string $label): self
    {
        if ($this->getValue($field) === '') {
            $this->addError($field, "Câmpul „{$label}” este obligatoriu.");
        }

        return $this;
    }

    public function email(string $field, string $label): self
    {
        $value = $this->getValue($field);

        if ($value !== '' && filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            $this->addError($field, "„{$label}” nu este o adresă de email validă.");
        }

        return $this;
    }

    public function minLength(string $field, string $label, int $minimum): self
    {
        $value = $this->getValue($field);

        if ($value !== '' && mb_strlen($value) < $minimum) {
            $this->addError($field, "„{$label}” trebuie să aibă cel puțin {$minimum} caractere.");
        }

        return $this;
    }

    public function maxLength(string $field, string $label, int $maximum): self
    {
        if (mb_strlen($this->getValue($field)) > $maximum) {
            $this->addError($field, "„{$label}” poate avea cel mult {$maximum} caractere.");
        }

        return $this;
    }

    public function number(string $field, string $label, float $minimum = 0): self
    {
        $value = $this->getValue($field);

        if ($value !== '' && (!is_numeric($value) || (float) $value < $minimum)) {
            $this->addError($field, "„{$label}” trebuie să fie un număr mai mare sau egal cu {$minimum}.");
        }

        return $this;
    }

    public function integer(string $field, string $label, int $minimum = 0, int $maximum = PHP_INT_MAX): self
    {
        $value = $this->getValue($field);
        $options = ['options' => ['min_range' => $minimum, 'max_range' => $maximum]];

        if ($value !== '' && filter_var($value, FILTER_VALIDATE_INT, $options) === false) {
            $this->addError($field, "„{$label}” trebuie să fie un număr întreg între {$minimum} și {$maximum}.");
        }

        return $this;
    }

    public function matches(string $field, string $otherField, string $label): self
    {
        if ($this->getValue($field) !== $this->getValue($otherField)) {
            $this->addError($field, "„{$label}” nu coincide.");
        }

        return $this;
    }

    public function pattern(string $field, string $regex, string $message): self
    {
        $value = $this->getValue($field);

        if ($value !== '' && preg_match($regex, $value) !== 1) {
            $this->addError($field, $message);
        }

        return $this;
    }

    /**
     * @param string[] $allowedValues
     */
    public function in(string $field, string $label, array $allowedValues): self
    {
        $value = $this->getValue($field);

        if ($value !== '' && !in_array($value, $allowedValues, true)) {
            $this->addError($field, "Valoarea aleasă pentru „{$label}” nu este permisă.");
        }

        return $this;
    }

    public function passes(): bool
    {
        return $this->errors === [];
    }

    /**
     * @return array<string, string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @throws ValidationException when at least one rule failed
     */
    public function validate(): void
    {
        if (!$this->passes()) {
            throw new ValidationException($this->errors);
        }
    }

    private function getValue(string $field): string
    {
        $value = $this->data[$field] ?? '';

        return is_scalar($value) ? trim((string) $value) : '';
    }

    /**
     * Only the first error of every field is kept.
     */
    private function addError(string $field, string $message): void
    {
        $this->errors[$field] ??= $message;
    }
}
