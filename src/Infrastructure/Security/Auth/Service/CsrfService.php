<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\Auth\Service;

use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\RequestStack;

final class CsrfService
{
    private const KEY = '_csrf';

    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function getToken(bool $refresh = false): string
    {
        $key = $this->requestStack->getSession()->get(self::KEY);

        if ($key === null || $refresh) {
            $key = $this->setNewKey();
        }

        return $key;
    }

    private function setNewKey(): string
    {
        $key = Uuid::uuid4()->toString();
        $this->requestStack->getSession()->set(self::KEY, $key);

        return $key;
    }
}