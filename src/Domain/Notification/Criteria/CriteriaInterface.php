<?php

declare(strict_types=1);

namespace App\Domain\Notification\Criteria;

interface CriteriaInterface
{
    public function notify(): void;
}