<?php

namespace App\DTO;

readonly class CsvClientData
{
    public function __construct(
        public string $name,
        public string $company,
        public string $street,
        public string $streetNumber,
        public string $postcode,
        public string $city,
        public string $country,
        public string $phone,
    ) {}
}

