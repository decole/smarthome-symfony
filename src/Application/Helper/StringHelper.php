<?php

namespace App\Application\Helper;


class StringHelper
{
    public static function sanitize(?string $sanitizeString, ?string $default = null): ?string
    {
        if ($sanitizeString === null || $sanitizeString === '') {
            return $default;
        }

        return htmlspecialchars($sanitizeString, \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function cleanReservedCharacters(?string $text): ?string
    {
        $characters = [
            '{',
            '}',
            '(',
            ')',
            '/',
            '\\',
            '@',
            ':',
        ];

        return str_replace($characters, '_', $text);
    }
}