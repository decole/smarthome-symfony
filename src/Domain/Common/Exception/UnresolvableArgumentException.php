<?php

declare(strict_types=1);

namespace App\Domain\Common\Exception;

use App\Application\Exception\HandledException;

final class UnresolvableArgumentException extends HandledException
{
    public static function argumentIsNotSet(string $argument): self
    {
        return new self("Required argument {$argument} is not set.", 400);
    }
}