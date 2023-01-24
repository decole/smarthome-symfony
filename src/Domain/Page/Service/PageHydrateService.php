<?php

namespace App\Domain\Page\Service;

use App\Application\Exception\DeviceDataException;
use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\Contract\Repository\FireSecurityRepositoryInterface;
use App\Domain\Contract\Repository\RelayRepositoryInterface;
use App\Domain\Contract\Repository\SecurityRepositoryInterface;
use App\Domain\Contract\Repository\SensorRepositoryInterface;
use App\Domain\FireSecurity\Entity\FireSecurity;
use App\Domain\Page\Entity\Page;
use App\Domain\Page\Factory\PageEntityDtoFactory;
use App\Domain\Relay\Entity\Relay;
use App\Domain\Security\Entity\Security;
use App\Domain\Sensor\Entity\Sensor;

final class PageHydrateService
{
    public function __construct(
        private readonly SensorRepositoryInterface $sensorRepository,
        private readonly RelayRepositoryInterface $relayRepository,
        private readonly SecurityRepositoryInterface $securityRepository,
        private readonly FireSecurityRepositoryInterface $fireSecurityRepository
    ) {
    }

    /**
     * @throws DeviceDataException
     */
    public function createEntityMap(Page $page): array
    {
        $result = [];

        foreach ($page->getConfig() as $type => $config)
        {
            foreach ($config as $id) {
                $result[] = (new PageEntityDtoFactory())->create($this->findEntity($type, $id));
            }
        }

        return $result;
    }

    public function createAllEntityMap(): array
    {
        // todo вызвать событие и забрать все данные с каждых сущностей
        $result = [];

        foreach ($this->sensorRepository->findAll() as $sensor) {
            $result[] = (new PageEntityDtoFactory())->create($sensor);
        }

        foreach ($this->relayRepository->findAll() as $relay) {
            $result[] = (new PageEntityDtoFactory())->create($relay);
        }

        foreach ($this->securityRepository->findAll() as $security) {
            $result[] = (new PageEntityDtoFactory())->create($security);
        }

        foreach ($this->fireSecurityRepository->findAll() as $fireSecurity) {
            $result[] = (new PageEntityDtoFactory())->create($fireSecurity);
        }

        return $result;
    }

    /**
     * @throws DeviceDataException
     */
    private function findEntity(string $type, string $id): EntityInterface
    {
        return match ($type) {
            // todo вызвать событие и забрать все данные с каждых сущностей
            Sensor::alias() => $this->sensorRepository->findById($id),
            Relay::alias() => $this->relayRepository->findById($id),
            Security::alias() => $this->securityRepository->findById($id),
            FireSecurity::alias() => $this->fireSecurityRepository->findById($id),

            default => throw DeviceDataException::notFoundPageEntity($id),
        };
    }
}