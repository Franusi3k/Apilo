<?php

namespace App\DTO;

class OrderResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $status,
        public readonly ?string $message = null,
        public readonly mixed $data = null,
        public readonly int $httpStatus = 200,
    ) {}

    public static function success(string $message, mixed $data = null): self
    {
        return new self(true, 'success', $message, $data);
    }

    public static function warning(string $message, mixed $data = null): self
    {
        return new self(false, 'warning', $message, $data, 409);
    }

    public static function error(string $message, mixed $data = null): self
    {
        return new self(false, 'error', $message, $data, 500);
    }
}