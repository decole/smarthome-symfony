<?php

namespace App\Domain\Contract\Repository;

use Doctrine\Common\Collections\Criteria;

/**
 * Contract for a Doctrine persistence layer ObjectRepository class to implement.
 *
 * @template-covariant T of object
 */
interface VisualNotificationRepositoryInterface
{
    /**
     * Поиск по типу с фильтром
     */
    public function findByTypeAndIsRead(
        ?int $type = null,
        ?bool $isRead = null
    ): array;

    /**
     * Пометить все прочитанное по типу либо все без типа нотификаций
     */
    public function setAllIsRead(?int $type = null): void;

    /**
     * Отдает данные согласно фильтру
     */
    public function findByFilters(Criteria $criteria): array;

    public function count(): int;
}