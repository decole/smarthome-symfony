<?php

declare(strict_types=1);

namespace App\Domain\Security\Service;

use App\Application\Helper\StringHelper;
use App\Application\Http\Web\Security\Dto\CrudSecurityDto;
use App\Domain\Common\Embedded\StatusMessage;
use App\Domain\Common\Enum\EntityStatusEnum;
use App\Domain\Common\Exception\UnresolvableArgumentException;
use App\Domain\Contract\CrudValidation\ValidationDtoInterface;
use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\Relay\Enum\RelayTypeEnum;
use App\Domain\Security\Entity\Security;
use App\Domain\Security\Enum\SecurityTypeEnum;
use App\Domain\Security\Factory\SecurityCrudFactory;
use App\Infrastructure\Doctrine\Traits\CommonCrudFieldTraits;
use App\Infrastructure\Doctrine\Traits\StatusMessageTrait;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class SecurityCrudService
{
    use StatusMessageTrait, CommonCrudFieldTraits;

    public function __construct(private readonly SecurityCrudFactory $crud)
    {
    }

    public function validate(CrudSecurityDto $dto, bool $isUpdate = false): ConstraintViolationListInterface
    {
        $this->crud->getValidationService()->setValue($dto);

        return $this->crud->validate($isUpdate);
    }

    /**
     * @throws OptimisticLockException|ORMException|JsonException
     */
    public function create(ValidationDtoInterface $dto): EntityInterface
    {
        assert($dto instanceof CrudSecurityDto);

        $entity = $this->getNewEntityByDto($dto);

        return $this->crud->save($entity);
    }

    public function list(): array
    {
        return $this->crud->list();
    }

    /**
     * @throws OptimisticLockException|ORMException|JsonException
     */
    public function update(string $id, CrudSecurityDto $dto): EntityInterface
    {
        $entity = $this->crud->getEntityById($id);

        assert($entity instanceof Security);

        $entity->setType($dto->type);

        $this->setDtoToEntityCommonParams($entity, $dto);

        $entity->setDetectPayload($dto->detectPayload);
        $entity->setHoldPayload($dto->holdPayload);
        $entity->setLastCommand($dto->lastCommand);

        $params = $dto->params === null ? [] :
            json_decode($this->prepareToJson($dto->params), true, 512, JSON_THROW_ON_ERROR) ?? [];

        $entity->setParams($params);

        $entity->setStatusMessage(new StatusMessage(
            $dto->message_info,
            $dto->message_ok,
            $dto->message_warn
        ));

        $entity->setStatus($dto->status === 'on' ?
            EntityStatusEnum::STATUS_ACTIVE->value : EntityStatusEnum::STATUS_DEACTIVATE->value);
        $entity->setNotify($dto->notify === 'on');
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

    /**
     * @return SecurityTypeEnum[]
     */
    public function getTypes(): array
    {
        return SecurityTypeEnum::cases();
    }

    public function createSecurityDto(?Request $request): CrudSecurityDto
    {
        $dto = new CrudSecurityDto();

        if ($request === null) {
            return $dto;
        }

        foreach ($request->request as $param => $value) {
            if (property_exists($dto, $param)) {
                $dto->$param = StringHelper::sanitize($value);
            }
        }

        return $dto;
    }

    /**
     * @throws JsonException
     */
    public function entityByDto(string $id): CrudSecurityDto
    {
        $entity = $this->crud->getEntityById($id);

        assert($entity instanceof Security);

        $dto = new CrudSecurityDto();

        $this->setEntityToDtoCommonParams($dto, $entity);

        $dto->detectPayload = $entity->getDetectPayload();
        $dto->holdPayload = $entity->getHoldPayload();
        $dto->lastCommand = $entity->getLastCommand();

        $params = $entity->getParams() === [] ? ['example' => 'empty'] : $entity->getParams();

        $dto->params = json_encode($params, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        $this->setStatusMessage($dto, $entity);

        $dto->status = $entity->getStatus() === EntityStatusEnum::STATUS_ACTIVE->value ? 'on' : null;

        return $dto;
    }

    /**
     * @param CrudSecurityDto $dto
     * @return Security
     * @throws JsonException|UnresolvableArgumentException
     */
    public function getNewEntityByDto(CrudSecurityDto $dto): Security
    {
        if (SecurityTypeEnum::tryFrom($dto->type) === null) {
            throw UnresolvableArgumentException::argumentIsNotSet('Security device type');
        }

        return new Security(
            securityType: $dto->type,
            name: $dto->name,
            topic: $dto->topic,
            payload: $dto->payload,
            detectPayload: $dto->detectPayload,
            holdPayload: $dto->holdPayload,
            lastCommand: $dto->lastCommand,
            params: $dto->params === '' || $dto->params === null ? [] :
                json_decode($this->prepareToJson($dto->params), true, 512, JSON_THROW_ON_ERROR) ?? [],
            statusMessage: new StatusMessage(
                $dto->message_info,
                $dto->message_ok,
                $dto->message_warn
            ),
            status: $dto->status === 'on' ?
                EntityStatusEnum::STATUS_ACTIVE->value : EntityStatusEnum::STATUS_DEACTIVATE->value,
            notify: $dto->notify === 'on',
        );
    }

    private function prepareToJson(string $value): string
    {
        return str_replace('&quot;', '"', $value);
    }
}