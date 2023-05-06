<?php

namespace App\Domain\Contract\Repository;

use App\Domain\VisualNotification\Entity\VisualNotification;

interface VisualNotificationRepositoryInterface
{
    /**
     * Поиск по типу с фильтром
     * @param int|null $type
     * @param bool|null $isRead
     * @return array<int, VisualNotification>
     */
    public function findByTypeAndIsRead(
        ?int $type = null,
        ?bool $isRead = null
    ): array;

    /**
     * Пометить все прочитанное по типу либо все без типа нотификаций
     * @param int|null $type
     * @return void
     */
    public function setAllIsRead(?int $type = null): void;
}