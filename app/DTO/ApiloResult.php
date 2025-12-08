<?php

namespace App\DTO;

class ApiloResult
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $message = null,
        public readonly mixed $data = null,
    ) {}

    public static function ok(mixed $data, ?string $message = null): self
    {
        return new self(true, $message, $data);
    }

    public static function fail(string $message, mixed $data = null): self
    {
        return new self(false, $message, $data);
    }
}
