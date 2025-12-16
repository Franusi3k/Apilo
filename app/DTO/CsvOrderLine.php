<?php

namespace App\DTO;

class CsvOrderLine
{
    public function __construct(
        public readonly string $name,
        public readonly int $quantity,
        public readonly float $price,
        public readonly string $sku,
        public readonly float $netto,
        public readonly string $currency,
        public readonly string $ean,
        public readonly array $client,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            quantity: (int) $data['quantity'],
            price: (float) $data['price'],
            sku: $data['sku'],
            netto: (float) $data['netto'],
            currency: $data['currency'],
            ean: $data['ean'],
            client: [
                'firstname' => $data['client_firstname'],
                'lastname' => $data['client_lastname'],
                'company' => $data['client_company'],
                'street' => $data['client_street'],
                'housenr' => $data['client_housenr'],
                'zip' => $data['client_zip'],
                'city' => $data['client_city'],
                'country' => $data['client_country'],
                'phone' => $data['client_phone'],
            ]
        );
    }
}
