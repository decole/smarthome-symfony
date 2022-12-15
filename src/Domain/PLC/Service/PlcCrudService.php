<?php

namespace App\Domain\PLC\Service;

use App\Application\Helper\StringHelper;
use App\Application\Http\Web\Plc\Dto\CrudPlcDto;
use App\Domain\Common\Embedded\StatusMessage;
use App\Domain\Contract\CrudValidation\ValidationDtoInterface;
use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\PLC\Entity\PLC;
use App\Domain\PLC\Factory\PlcCrudFactory;
use App\Infrastructure\Doctrine\Traits\StatusMessageTrait;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class PlcCrudService
{
    use StatusMessageTrait;

    public function __construct(private PlcCrudFactory $crud)
    {
    }

    public function validate(CrudPlcDto $dto, bool $isUpdate = false): ConstraintViolationListInterface
    {
        $this->crud->getValidationService()->setValue($dto);

        return $this->crud->validate($isUpdate);
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    public function create(ValidationDtoInterface $dto): EntityInterface
    {
        assert($dto instanceof CrudPlcDto);

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
    public function update(string $id, CrudPlcDto $dto): EntityInterface
    {
        $entity = $this->crud->getEntityById($id);

        assert($entity instanceof PLC);

        $entity->setName($dto->name);
        $entity->setTargetTopic($dto->targetTopic);
        $entity->setAlarmSecondDelay($dto->alarmSecondDelay);

        $entity->setStatusMessage(new StatusMessage(
            $dto->message_info,
            $dto->message_ok,
            $dto->message_warn
        ));

        $entity->setStatus($dto->status === 'on' ? PLC::STATUS_ACTIVE : PLC::STATUS_DEACTIVATE);
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

    public function createDto(?Request $request): CrudPlcDto
    {
        $dto = new CrudPlcDto();

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

    public function entityByDto(string $id): CrudPlcDto
    {
        $entity = $this->crud->getEntityById($id);

        assert($entity instanceof PLC);

        $dto = new CrudPlcDto();

        $dto->name = $entity->getName();
        $dto->targetTopic = $entity->getTargetTopic();
        $dto->alarmSecondDelay = $entity->getAlarmSecondDelay();

        $this->setStatusMessage($dto, $entity);

        $dto->notify = $entity->isNotify() === true ? 'on' : null;
        $dto->status = $entity->getStatus() === PLC::STATUS_ACTIVE ? 'on' : null;

        return $dto;
    }

    /**
     * @param CrudPlcDto $dto
     * @return PLC
     */
    public function getNewEntityByDto(CrudPlcDto $dto): PLC
    {
        return new PLC(
            name: $dto->name,
            targetTopic: $dto->targetTopic,
            alarmSecondDelay: $dto->alarmSecondDelay,
            statusMessage: new StatusMessage(
                $dto->message_info,
                $dto->message_ok,
                $dto->message_warn
            ),
            status: $dto->status === 'on' ? PLC::STATUS_ACTIVE : PLC::STATUS_DEACTIVATE,
            notify: $dto->notify === 'on',
        );
    }
}