<?php

namespace App\Domain\Page\Service;

use App\Application\Exception\DeviceDataException;
use App\Domain\Contract\Repository\PageRepositoryInterface;

final class SitePageService
{
    public function __construct(
        private PageRepositoryInterface $repository,
        private PageGidratorService $service
    ) {
    }

    /**
     * @throws DeviceDataException
     */
    public function getDeviceList(string $name): array
    {
        $page = $this->repository->findByName($name);

        if ($page === null) {
            return [];
        }

        return $this->service->createEntityMap($page);
    }

    public function getAllDeviceList(): array
    {
        return $this->service->createAllEntityMap();
    }
}