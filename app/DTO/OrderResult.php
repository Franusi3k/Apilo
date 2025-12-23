<?php

namespace App\DTO;

readonly class OrderResult
{
    public function __construct(
        public bool $success,
        public string $status,
        public ?string $message = null,
        public mixed $data = null,
        public int $httpStatus = 200,
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
