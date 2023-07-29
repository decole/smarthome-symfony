<?php

declare(strict_types=1);

namespace App\Infrastructure\TwoFactor\Dto;

class TwoFactorResultDto
{
    public function __construct(public bool $isCorrect, public ?string $error = null)
    {
    }
}