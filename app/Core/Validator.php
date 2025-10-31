<?php
namespace App\Core;

class Validator
{
    public static function required(array $fields, array $data): array
    {
        $errors = [];
        foreach ($fields as $field) {
            if (empty(trim($data[$field] ?? ''))) {
                $errors[$field] = ucfirst($field) . ' is required.';
            }
        }
        return $errors;
    }

    public static function email(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
