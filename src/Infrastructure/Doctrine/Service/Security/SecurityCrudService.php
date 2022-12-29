<?php

namespace App\Infrastructure\Doctrine\Service\Security;

use App\Application\Helper\StringHelper;
use App\Application\Http\Web\Security\Dto\CrudSecurityDto;
use App\Domain\Common\Embedded\StatusMessage;
use App\Domain\Contract\CrudValidation\ValidationDtoInterface;
use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\Security\Entity\Security;
use App\Infrastructure\Doctrine\Service\Security\Factory\SecurityCrudFactory;
use App\Infrastructure\Doctrine\Traits\CommonCrudFieldTraits;
use App\Infrastructure\Doctrine\Traits\StatusMessageTrait;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Webmozart\Assert\Assert;

final class SecurityCrudService
{
    use StatusMessageTrait, CommonCrudFieldTraits;

    public function __construct(private SecurityCrudFactory $crud)
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
            json_decode(str_replace('&quot;', '"', $dto->params), true, 512, JSON_THROW_ON_ERROR) ?? [];

        $entity->setParams($params);

        $entity->setStatusMessage(new StatusMessage(
            $dto->message_info,
            $dto->message_ok,
            $dto->message_warn
        ));

        $entity->setStatus($dto->status === 'on' ? Security::STATUS_ACTIVE : Security::STATUS_DEACTIVATE);
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

    public function getTypes(): array
    {
        return Security::SECURITY_TYPES;
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

        $dto->status = $entity->getStatus() === Security::STATUS_ACTIVE ? 'on' : null;

        return $dto;
    }

    /**
     * @param CrudSecurityDto $dto
     * @return Security
     * @throws JsonException
     */
    public function getNewEntityByDto(CrudSecurityDto $dto): Security
    {
        Assert::inArray($dto->type, $this->getTypes());

        return new Security(
            securityType: $dto->type,
            name: $dto->name,
            topic: $dto->topic,
            payload: $dto->payload,
            detectPayload: $dto->detectPayload,
            holdPayload: $dto->holdPayload,
            lastCommand: $dto->lastCommand,
            params: $dto->params === '' || $dto->params === null ? [] :
                json_decode($dto->params, true, 512, JSON_THROW_ON_ERROR) ?? [],
            statusMessage: new StatusMessage(
                $dto->message_info,
                $dto->message_ok,
                $dto->message_warn
            ),
            status: $dto->status === 'on' ? Security::STATUS_ACTIVE : Security::STATUS_DEACTIVATE,
            notify: $dto->notify === 'on',
        );
    }
}