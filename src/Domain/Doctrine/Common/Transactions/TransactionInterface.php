<?php

namespace App\Domain\Doctrine\Common\Transactions;

interface TransactionInterface
{
    public function transactional(callable $scope, ?callable $failOver = null);

    public function flush($entity = null): void;

    public function commit(...$entities): void;
}
