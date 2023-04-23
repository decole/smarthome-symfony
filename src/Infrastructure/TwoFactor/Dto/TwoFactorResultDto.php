<?php

namespace App\Infrastructure\TwoFactor\Dto;

class TwoFactorResultDto
{
    public function __construct(public bool $isCorrect, public ?string $error = null)
    {
    }
}