<?php

if (! function_exists('parsePrice')) {
    function parsePrice(string|float|int|null $value): float
    {
        if ($value === null) {
            return 0.0;
        }

        $str = trim((string)$value);

        $str = str_replace(['PLN', ' '], '', $str);

        $str = str_replace(',', '.', $str);

        $str = preg_replace('/[^0-9.]/', '', $str);

        if (str_contains($str, '.')) {
            [$int, $dec] = explode('.', $str, 2);
            $str = $int . '.' . substr($dec, 0, 2);
        }

        return (float)$str;
    }
}

