<?php

namespace App\DTO;

readonly class ApiloResult
{
    public function __construct(
        public bool $success,
        public ?string $message = null,
        public mixed $data = null,
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
