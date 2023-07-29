<?php

declare(strict_types=1);

namespace App\Domain\Page\Service;

use App\Application\Exception\DeviceDataException;
use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\Event\EntityListEvent;
use App\Domain\Page\Entity\Page;
use App\Domain\Page\Factory\PageEntityDtoFactory;
use Psr\EventDispatcher\EventDispatcherInterface;

final class PageHydrateService
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    /**
     * @throws DeviceDataException
     */
    public function createEntityMap(Page $page): array
    {
        $result = [];

        $mapByEntityType = $this->eventDispatcher->dispatch(new EntityListEvent(), EntityListEvent::NAME);

        foreach ($page->getConfig() as $type => $config)
        {
            foreach ($config as $id) {
                $result[] = (new PageEntityDtoFactory())->create($this->findEntity(
                    type: $type,
                    id: $id,
                    map: $mapByEntityType->getEntityMap()
                ));
            }
        }

        return $result;
    }

    public function createAllEntityMap(): array
    {
        $result = [];

        /** @var EntityListEvent $eventResult */
        $eventResult = $this->eventDispatcher->dispatch(new EntityListEvent(), EntityListEvent::NAME);

        /** @var EntityInterface[] $list */
        foreach ($eventResult->getEntityMap() as $list) {
            foreach ($list as $entity) {
                $result[] = (new PageEntityDtoFactory())->create($entity);
            }
        }

        return $result;
    }

    /**
     * @throws DeviceDataException
     */
    private function findEntity(string $type, string $id, array $map): EntityInterface
    {
        /** @var EntityInterface $entity */
        foreach ($map[$type] as $entity) {
            if ($entity->getIdToString() === $id) {
                return $entity;
            }
        }

        throw DeviceDataException::notFoundPageEntity($id);
    }
}