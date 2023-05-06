<?php

namespace App\Domain\Page\Service;

use App\Application\Helper\StringHelper;
use App\Application\Http\Web\Page\Dto\CrudPageDto;
use App\Domain\Contract\CrudValidation\ValidationDtoInterface;
use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\Contract\Repository\FireSecurityRepositoryInterface;
use App\Domain\Contract\Repository\RelayRepositoryInterface;
use App\Domain\Contract\Repository\SecurityRepositoryInterface;
use App\Domain\Contract\Repository\SensorRepositoryInterface;
use App\Domain\Page\Entity\Page;
use App\Domain\Page\Factory\PageCrudFactory;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class PageCrudService
{
    private const DEFAULT_NAME = 'new page';
    private const NAME_ALIAS = 'name';
    private const ALIAS_FIELD_ALIAS = 'alias';
    private const ICON_ALIAS = 'icon';
    private const GROUP_ALIAS = 'groupId';

    public function __construct(
        private readonly PageCrudFactory $crud,
        private readonly SensorRepositoryInterface $sensorRepository,
        private readonly RelayRepositoryInterface $relayRepository,
        private readonly SecurityRepositoryInterface $securityRepository,
        private readonly FireSecurityRepositoryInterface $fireSecurityRepository
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
        $entity->setAlias($dto->alias);
        $entity->setIcon($dto->icon);
        $entity->setGroupId($dto->groupId);
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

    public function createDto(?Request $request): CrudPageDto
    {
        $dto = new CrudPageDto();

        if ($request === null || $request->request->get(self::NAME_ALIAS) === null) {
            $this->setDefault($dto);

            return $dto;
        }

        $dto->name = (string)$request->request->get(self::NAME_ALIAS);
        $dto->config = $this->getConfigByRequest($request);
        $dto->alias = (string)$request->request->get(self::ALIAS_FIELD_ALIAS);
        $dto->icon = (string)$request->request->get(self::ICON_ALIAS);
        $dto->groupId = (int)$request->request->get(self::GROUP_ALIAS);

        return $dto;
    }

    public function entityByDto(string $id): CrudPageDto
    {
        $entity = $this->crud->getEntityById($id);

        assert($entity instanceof Page);

        $dto = new CrudPageDto();

        $dto->name = $entity->getName();
        $dto->config = $entity->getConfig();
        $dto->alias = $entity->getAliasUri();
        $dto->icon = $entity->getIcon();
        $dto->groupId = $entity->getGroupId();

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
            config: $dto->config,
            icon: $dto->icon,
            alias: $dto->alias,
            groupId: $dto->groupId
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

    public function getSanitizeRequest(?Request $request, string $name): array
    {
        $result = [];
        $list = $request?->request->get($name) ?? [];

        foreach ($list as $id) {
            $result[] = StringHelper::sanitize($id);
        }

        return $result;
    }

    private function setDefault(CrudPageDto $dto): void
    {
        $dto->name = self::DEFAULT_NAME;
        $dto->config = $this->getConfigByRequest(null);
        $dto->groupId = 0;
        $dto->icon = 'fas fa-home';
        $dto->alias = 'example';
    }

    /**
     * @param Request|null $request
     * @return array<string, array<string, string>>
     */
    private function getConfigByRequest(?Request $request): array
    {
        return [
            'sensor' => $this->getSanitizeRequest($request, 'sensor'),
            'relay' => $this->getSanitizeRequest($request, 'relay'),
            'security' => $this->getSanitizeRequest($request, 'security'),
            'fireSecurity' => $this->getSanitizeRequest($request, 'fireSecurity'),
        ];
    }
}