<?php

namespace App\DTO;

class OrderItemsResult
{
    public function __construct(
        public array $items,
        public float $totalNet,
        public float $totalGross,
        public array $errors,
    ) {}
}