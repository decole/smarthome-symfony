<?php

namespace App\Infrastructure\Doctrine\Service\Page;

use App\Application\Helper\StringHelper;
use App\Application\Http\Web\Page\Dto\CrudPageDto;
use App\Domain\Contract\CrudValidation\ValidationDtoInterface;
use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\Contract\Repository\FireSecurityRepositoryInterface;
use App\Domain\Contract\Repository\RelayRepositoryInterface;
use App\Domain\Contract\Repository\SecurityRepositoryInterface;
use App\Domain\Contract\Repository\SensorRepositoryInterface;
use App\Domain\Doctrine\Page\Entity\Page;
use App\Domain\Doctrine\Relay\Entity\Relay;
use App\Infrastructure\Doctrine\Service\Page\Factory\PageCrudFactory;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class PageCrudService
{
    public function __construct(
        private PageCrudFactory $crud,
        private SensorRepositoryInterface $sensorRepository,
        private RelayRepositoryInterface $relayRepository,
        private SecurityRepositoryInterface $securityRepository,
        private FireSecurityRepositoryInterface $fireSecurityRepository
    ) {
    }

    public function validate(CrudPageDto $relayDto, bool $isUpdate = false): ConstraintViolationListInterface
    {
        $this->crud->getValidationService()->setValue($relayDto);

        return $this->crud->validate($isUpdate);
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    public function create(ValidationDtoInterface $dto): EntityInterface
    {
        assert($dto instanceof CrudPageDto);

        $entity = $this->getNewEntityByDto($dto);

        return $this->crud->save($entity);
    }

    public function list(): array
    {
        return $this->crud->list();
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    public function update(string $id, CrudPageDto $dto): EntityInterface
    {
        $entity = $this->crud->getEntityById($id);

        assert($entity instanceof Page);

        $entity->setName($dto->name);
        $entity->setConfig($dto->config);
        $entity->onUpdated();

        return $this->crud->save($entity);
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    public function delete(string $id): void
    {
        $entity = $this->crud->getEntityById($id);

        if ($entity) {
            $this->crud->delete($entity);
        }
    }

    public function getTypes(): array
    {
        return Relay::RELAY_TYPES;
    }

    public function createDto(?Request $request): CrudPageDto
    {
        $dto = new CrudPageDto();

        if ($request === null) {
            return $dto;
        }

        $config = [
            'sensor' => $this->getSanitizeRequest($request, 'sensor'),
            'relay' => $this->getSanitizeRequest($request, 'relay'),
            'security' => $this->getSanitizeRequest($request, 'security'),
            'fireSecurity' => $this->getSanitizeRequest($request, 'fireSecurity'),
        ];

        $dto->name = $request->request->get('name') ?? 'new page';
        $dto->config = $config;

        return $dto;
    }

    public function entityByDto(string $id): CrudPageDto
    {
        $entity = $this->crud->getEntityById($id);

        assert($entity instanceof Page);

        $dto = new CrudPageDto();

        $dto->name = $entity->getName();
        $dto->config = $entity->getConfig();

        return $dto;
    }

    /**
     * @param CrudPageDto $dto
     * @return Page
     */
    public function getNewEntityByDto(CrudPageDto $dto): Page
    {
        return new Page(
            name: $dto->name,
            config: $dto->config
        );
    }

    public function getSelectedDeviceList(CrudPageDto $dto): array
    {
        $config = $dto->config;
        $sensors = $this->sensorRepository->findAll();
        $relays = $this->relayRepository->findAll();
        $security = $this->securityRepository->findAll();
        $fireSecurity = $this->fireSecurityRepository->findAll();

        return [
            'sensors' => $this->getDevicesJoinConfig($sensors, $config['sensor'] ?? []),
            'relays' => $this->getDevicesJoinConfig($relays, $config['relay'] ?? []),
            'security' => $this->getDevicesJoinConfig($security, $config['security'] ?? []),
            'fire_security' => $this->getDevicesJoinConfig($fireSecurity, $config['fireSecurity'] ?? []),
        ];
    }

    private function getDevicesJoinConfig(array $devices, array $deviceByConfig): array
    {
        $deviceJoinConfig = [];

        foreach ($devices as $device) {
            $sensorId = $device->getId()->toString();

            $deviceJoinConfig[] = [
                'id' => $sensorId,
                'name' => $device->getName(),
                'selected' => $this->isJoined($deviceByConfig, $sensorId),
            ];
        }

        return $deviceJoinConfig;
    }

    private function isJoined(array $devices, string $deviceId): bool
    {
        return in_array($deviceId, $devices ?? [], true);
    }

    public function getSanitizeRequest(Request $request, string $name): array
    {
        $result = [];
        $list = $request->request->get($name);

        if ($list === null) {
            return $result;
        }

        foreach ($list as $id) {
            $result[] = StringHelper::sanitize($id);
        }

        return $result;
    }
}
