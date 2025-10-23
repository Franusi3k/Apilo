<?php

if (! function_exists('safeConvert')) {
    function safeConvert(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        $encoding = mb_detect_encoding($value, ['UTF-8', 'ISO-8859-1', 'ISO-8859-2', 'ASCII'], true);

        if ($encoding === false) {
            $encoding = 'ISO-8859-1';
        }

        try {
            $encoded = mb_convert_encoding($value, 'UTF-8', $encoding);
        } catch (\ValueError $e) {
            $encoded = $value;
        }

        return $encoded ?: (string) $value;
    }
}
