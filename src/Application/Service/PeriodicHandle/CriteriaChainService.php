<?php

namespace App\Application\Service\PeriodicHandle;

use App\Application\Service\PeriodicHandle\Criteria\PeriodicHandleCriteriaInterface;

final class CriteriaChainService
{
    /**
     * @var array<string, PeriodicHandleCriteriaInterface>
     */
    private array $criteria;

    public function __construct()
    {
        $this->criteria = [];
    }

    /**
     * @see App\DependencyInjection\PeriodicHandleCriteriaCompiler
     *
     * @param PeriodicHandleCriteriaInterface $criteria
     * @param string $alias
     * @return void
     */
    public function addCriteria(PeriodicHandleCriteriaInterface $criteria, string $alias): void
    {
        $this->criteria[$alias] = $criteria;
    }

    public function getCriteriaMap(): array
    {
        return $this->criteria;
    }
}