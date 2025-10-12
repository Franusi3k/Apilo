<?php

if (! function_exists('splitStreetAndNumber')) {
    function splitStreetAndNumber(string $streetFull): array
    {
        $streetFull = trim($streetFull);

        if (preg_match('/^(.*?)[,\s]+(\d+\w*(?:\/\d+\w*)?)$/', $streetFull, $matches)) {
            $streetName = trim($matches[1]);
            $streetNumber = trim($matches[2]);
        } else {
            $streetName = $streetFull;
            $streetNumber = '';
        }

        return [$streetName, $streetNumber];
    }
}
