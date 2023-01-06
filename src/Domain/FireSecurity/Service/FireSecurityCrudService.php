<?php

namespace App\Domain\FireSecurity\Service;

use App\Application\Helper\StringHelper;
use App\Application\Http\Web\FireSecurity\Dto\CrudFireSecurityDto;
use App\Domain\Common\Embedded\StatusMessage;
use App\Domain\Common\Enum\EntityStatusEnum;
use App\Domain\Contract\CrudValidation\ValidationDtoInterface;
use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\FireSecurity\Entity\FireSecurity;
use App\Domain\FireSecurity\Factory\FireSecurityCrudFactory;
use App\Domain\Security\Entity\Security;
use App\Infrastructure\Doctrine\Traits\CommonCrudFieldTraits;
use App\Infrastructure\Doctrine\Traits\StatusMessageTrait;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class FireSecurityCrudService
{
    use StatusMessageTrait, CommonCrudFieldTraits;

    public function __construct(private FireSecurityCrudFactory $crud)
    {
    }

    public function validate(CrudFireSecurityDto $dto, bool $isUpdate = false): ConstraintViolationListInterface
    {
        $this->crud->getValidationService()->setValue($dto);

        return $this->crud->validate($isUpdate);
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    public function create(ValidationDtoInterface $dto): EntityInterface
    {
        assert($dto instanceof CrudFireSecurityDto);

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
    public function update(string $id, CrudFireSecurityDto $dto): EntityInterface
    {
        $entity = $this->crud->getEntityById($id);

        assert($entity instanceof FireSecurity);

        $this->setDtoToEntityCommonParams($entity, $dto);

        $entity->setNormalPayload($dto->normalPayload);
        $entity->setAlertPayload($dto->alertPayload);
        $entity->setLastCommand($dto->lastCommand);

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

    public function createFireSecurityDto(?Request $request): CrudFireSecurityDto
    {
        $dto = new CrudFireSecurityDto();

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

    public function entityByDto(string $id): CrudFireSecurityDto
    {
        $entity = $this->crud->getEntityById($id);

        assert($entity instanceof FireSecurity);

        $dto = new CrudFireSecurityDto();

        $this->setEntityToDtoCommonParams($dto, $entity, false);

        $dto->normalPayload = $entity->getNormalPayload();
        $dto->alertPayload = $entity->getAlertPayload();
        $dto->lastCommand = $entity->getLastCommand();

        $this->setStatusMessage($dto, $entity);

        $dto->status = $entity->getStatus() === EntityStatusEnum::STATUS_ACTIVE->value ? 'on' : null;

        return $dto;
    }

    public function getNewEntityByDto(CrudFireSecurityDto $dto): FireSecurity
    {
        return new FireSecurity(
            name: $dto->name,
            topic: $dto->topic,
            payload: $dto->payload,
            normalPayload: $dto->normalPayload,
            alertPayload: $dto->alertPayload,
            lastCommand: $dto->lastCommand,
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
}