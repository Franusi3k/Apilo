<?php

namespace App\DTO;

class CsvClientData
{
    public function __construct(
        public readonly string $name,
        public readonly string $company,
        public readonly string $street,
        public readonly string $streetNumber,
        public readonly string $postcode,
        public readonly string $city,
        public readonly string $country,
        public readonly string $phone,
    ) {}
}

