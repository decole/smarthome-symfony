<?php

declare(strict_types=1);

namespace App\Application\Presenter\Api;

interface PresenterInterface
{
    public function present(): array;
}
