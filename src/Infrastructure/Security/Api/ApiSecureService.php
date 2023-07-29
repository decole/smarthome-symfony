<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\Api;

/**
 * Т.к. jquery post не умеет http headers, security token пришлось отправлять как параметр формы
 */
final class ApiSecureService
{
    public function __construct(private readonly string $targetToken)
    {
    }

    public function validate(?string $token): bool
    {
        if ($token === null) {
            return false;
        }

        return $this->targetToken === $token;
    }
}