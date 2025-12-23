<?php

namespace App\DTO;

readonly class CsvOrderLine
{
    public function __construct(
        public string $name,
        public int $quantity,
        public float $price,
        public string $sku,
        public float $netto,
        public string $currency,
        public string $ean,
        public array $client,
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
