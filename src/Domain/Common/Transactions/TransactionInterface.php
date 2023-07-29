<?php

declare(strict_types=1);

namespace App\Domain\Common\Transactions;

interface TransactionInterface
{
    public function transactional(callable $scope, ?callable $failOver = null);

    public function flush($entity = null): void;

    public function commit(...$entities): void;
}