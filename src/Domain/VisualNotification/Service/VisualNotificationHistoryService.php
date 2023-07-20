<?php

declare(strict_types=1);

namespace App\Domain\VisualNotification\Service;

use App\Application\Http\Web\VisualNotification\Dto\VisualNotificationHistoryInputDto;
use App\Domain\Contract\Repository\VisualNotificationRepositoryInterface;
use App\Domain\VisualNotification\Dto\VisualNotificationResultDto;
use Doctrine\Common\Collections\Criteria;

final class VisualNotificationHistoryService
{
    public function __construct(private readonly VisualNotificationRepositoryInterface $repository)
    {
    }

    public function paginate(VisualNotificationHistoryInputDto $dto): VisualNotificationResultDto
    {
        $criteria = Criteria::create();

        $criteria->setFirstResult(($dto->page - 1) * $dto->limit);
        $criteria->setMaxResults($dto->limit);
        $criteria->orderBy(['createdAt' => Criteria::DESC]);

        $current = $dto->page;
        $next = ++$dto->page;

        return new VisualNotificationResultDto(
            $this->repository->findByFilters($criteria),
            $this->getPages($dto),
            $this->gerPrev($current),
            $next,
            $current
        );
    }

    private function gerPrev(int $current): int
    {
        if ($current === 1) {
            $prev = 1;
        } else {
            $prev = $current - 1;
        }

        return $prev;
    }

    public function getPages(VisualNotificationHistoryInputDto $dto): int
    {
        $value = (int)(floor($this->repository->count() / $dto->limit));

        if ($value === 0) {
            return 1;
        }

        return $value;
    }
}