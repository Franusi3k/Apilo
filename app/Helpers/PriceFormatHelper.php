<?php

if (! function_exists('parsePrice')) {
    /**
     * Parsuje wartość ceny z formatu tekstowego na float
     *
     * @param  mixed  $value
     */
    function parsePrice(float $value): float
    {
        try {
            $str = strval($value);
            $str = str_replace(['PLN', ','], ['', '.'], $str);

            return floatval(trim($str));
        } catch (Exception) {
            return 0.0;
        }
    }
}
