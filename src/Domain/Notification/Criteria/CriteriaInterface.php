<?php

namespace App\Domain\Notification\Criteria;

interface CriteriaInterface
{
    public function notify(): void;
}