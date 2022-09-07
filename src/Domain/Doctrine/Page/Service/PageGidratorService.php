<?php

namespace App\Domain\Doctrine\Page\Service;

use App\Application\Exception\DeviceDataException;
use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\Contract\Repository\FireSecurityRepositoryInterface;
use App\Domain\Contract\Repository\RelayRepositoryInterface;
use App\Domain\Contract\Repository\SecurityRepositoryInterface;
use App\Domain\Contract\Repository\SensorRepositoryInterface;
use App\Domain\Doctrine\FireSecurity\Entity\FireSecurity;
use App\Domain\Doctrine\Page\Entity\Page;
use App\Domain\Doctrine\Page\Factory\PageEntityDtoFactory;
use App\Domain\Doctrine\Relay\Entity\Relay;
use App\Domain\Doctrine\Security\Entity\Security;
use App\Domain\Doctrine\Sensor\Entity\Sensor;

final class PageGidratorService
{
    public function __construct(
        private SensorRepositoryInterface $sensorRepository,
        private RelayRepositoryInterface $relayRepository,
        private SecurityRepositoryInterface $securityRepository,
        private FireSecurityRepositoryInterface $fireSecurityRepository
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
            Sensor::alias() => $this->sensorRepository->findById($id),
            Relay::alias() => $this->relayRepository->findById($id),
            Security::alias() => $this->securityRepository->findById($id),
            FireSecurity::alias() => $this->fireSecurityRepository->findById($id),

            default => throw DeviceDataException::notFoundPageEntity($id),
        };
    }
}
