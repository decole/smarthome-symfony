<?php

namespace App\Application\Service\PeriodicHandle\Criteria;

interface PeriodicHandleCriteriaInterface
{
    /**
     * Имя для dependency injection tag map
     * @see App\DependencyInjection\PeriodicHandleCriteriaCompiler
     *
     * @return string
     */
    public static function alias(): string;

    /**
     * Определяющее условие, по которому оценивается, нужно ли запускать критерию
     *
     * @return bool
     */
    public function isDue(): bool;

    public function execute(): void;
}