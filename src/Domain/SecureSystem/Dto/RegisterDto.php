<?php

namespace App\Domain\SecureSystem\Dto;

use Symfony\Component\HttpFoundation\Request;

final class RegisterDto
{
    private ?string $name;
    private ?string $email;
    private ?string $password;
    private ?string $rePassword;
    private ?string $terms;
    private ?string $csrf;

    public function __construct(Request $request)
    {
        $this->name = htmlspecialchars($request->get('name'), \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8');
        $this->email = htmlspecialchars($request->get('email'), \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8');
        $this->password = htmlspecialchars($request->get('password'), \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8');
        $this->rePassword = htmlspecialchars($request->get('repassword'), \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8');
        $this->terms = htmlspecialchars($request->get('terms'), \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8');
        $this->csrf = htmlspecialchars($request->get('csrf'), \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8');
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getRePassword(): ?string
    {
        return $this->rePassword;
    }

    public function getTerms(): ?string
    {
        return $this->terms;
    }

    public function getCsrf(): ?string
    {
        return $this->csrf;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'password' => $this->getPassword(),
            'rePassword' => $this->getRePassword(),
            'terms' => $this->getTerms(),
            'csrf' => $this->getCsrf(),
        ];
    }
}