<?php

namespace App\Application\Service\Alert\Criteria;

interface CriteriaInterface
{
    public function notify(): void;
}