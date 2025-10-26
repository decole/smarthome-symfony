<?php

declare(strict_types=1);

namespace App\Application\Helper;

class StringHelper
{
    public static function sanitize(mixed $sanitizeString, ?string $default = null): mixed
    {
        if (\is_array($sanitizeString)) {
            return $sanitizeString;
        }

        if (null === $sanitizeString || '' === $sanitizeString) {
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
