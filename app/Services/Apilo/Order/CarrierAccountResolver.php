<?php

namespace App\Services\Apilo\Order;

class CarrierAccountResolver
{
    private const MAP = [
        'Eurohermes' => 9,
        'RohligSuus' => 69,
    ];

    public static function resolve(string $deliveryMethod): ?int
    {
        return self::MAP[$deliveryMethod] ?? null;
    }
}
