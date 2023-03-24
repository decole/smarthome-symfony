<?php

namespace App\Infrastructure\Security\Auth\Service;

use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class CsrfService
{
    private const KEY = '_csrf';

    public function __construct(private readonly SessionInterface $session)
    {
    }

    public function getToken(bool $refresh = false): string
    {
        $key = $this->session->get(self::KEY);

        if ($key === null || $refresh) {
            $key = $this->setNewKey();
        }



        return $key;
    }

    private function setNewKey(): string
    {
        $key = Uuid::uuid4()->toString();
        $this->session->set(self::KEY, $key);

        return $key;
    }
}