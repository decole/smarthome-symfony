<?php


namespace App\Infrastructure\Doctrine\Service\Relay;


use App\Application\Helper\StringHelper;
use App\Application\Http\Web\Relay\Dto\CrudRelayDto;
use App\Application\Service\Validation\ValidationDtoInterface;
use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\Doctrine\Common\Embedded\StatusMessage;
use App\Domain\Doctrine\Relay\Entity\Relay;
use App\Domain\Doctrine\Sensor\Entity\Sensor;
use App\Infrastructure\Doctrine\Service\Relay\Factory\RelayCrudFactory;
use App\Infrastructure\Doctrine\Traits\CommonCrudFieldTraits;
use App\Infrastructure\Doctrine\Traits\StatusMessageTrait;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Webmozart\Assert\Assert;

final class RelayCrudService
{
    use StatusMessageTrait, CommonCrudFieldTraits;

    public function __construct(private RelayCrudFactory $crud)
    {
    }

    public function validate(CrudRelayDto $relayDto, bool $isUpdate = false): ConstraintViolationListInterface
    {
        $this->crud->getValidationService()->setValue($relayDto);

        return $this->crud->validate($isUpdate);
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    public function create(ValidationDtoInterface $dto): EntityInterface
    {
        assert($dto instanceof CrudRelayDto);

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
    public function update(string $id, CrudRelayDto $dto): EntityInterface
    {
        $entity = $this->crud->getEntityById($id);

        assert($entity instanceof Relay);

        $this->setDtoToEntityCommonParams($entity, $dto);

        $entity->setType($dto->type);
        $entity->setLastCommand($dto->lastCommand);
        $entity->setCommandOn($dto->commandOn);
        $entity->setCommandOff($dto->commandOff);
        $entity->setIsFeedbackPayload($dto->isFeedbackPayload === 'on');
        $entity->setCheckTopic($dto->checkTopic);
        $entity->setCheckTopicPayloadOn($dto->checkTopicPayloadOn);
        $entity->setCheckTopicPayloadOff($dto->checkTopicPayloadOff);

        $entity->setStatusMessage(new StatusMessage(
            $dto->message_info,
            $dto->message_ok,
            $dto->message_warn
        ));

        $entity->setStatus($dto->status === 'on' ? Relay::STATUS_ACTIVE : Relay::STATUS_DEACTIVATE);
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
        return Relay::RELAY_TYPES;
    }

    public function createDto(?Request $request): CrudRelayDto
    {
        $dto = new CrudRelayDto();

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

    public function entityByDto(string $id): CrudRelayDto
    {
        $entity = $this->crud->getEntityById($id);

        assert($entity instanceof Relay);

        $dto = new CrudRelayDto();

        $this->setEntityToDtoCommonParams($dto, $entity);

        $dto->commandOn = $entity->getCommandOn();
        $dto->commandOff = $entity->getCommandOff();
        $dto->lastCommand = $entity->getLastCommand();

        $dto->isFeedbackPayload = $entity->isFeedbackPayload() === true ? 'on' : null;
        $dto->checkTopic = $entity->getCheckTopic();
        $dto->checkTopicPayloadOn = $entity->getCheckTopicPayloadOn();
        $dto->checkTopicPayloadOff = $entity->getCheckTopicPayloadOff();

        $this->setStatusMessage($dto, $entity);

        $dto->status = $entity->getStatus() === Sensor::STATUS_ACTIVE ? 'on' : null;

        return $dto;
    }

    /**
     * @param CrudRelayDto $dto
     * @return Relay
     */
    public function getNewEntityByDto(CrudRelayDto $dto): Relay
    {
        Assert::inArray($dto->type, $this->getTypes());

        return new Relay(
            type: $dto->type,
            name: $dto->name,
            topic: $dto->topic,
            payload: $dto->payload,
            commandOn: $dto->commandOn,
            commandOff: $dto->commandOff,
            checkTopic: $dto->checkTopic,
            checkTopicPayloadOn: $dto->checkTopicPayloadOn,
            checkTopicPayloadOff: $dto->checkTopicPayloadOff,
            lastCommand: null,
            isFeedbackPayload: $dto->isFeedbackPayload === 'on',
            statusMessage: new StatusMessage(
                $dto->message_info,
                $dto->message_ok,
                $dto->message_warn
            ),
            status: $dto->status === 'on' ? Sensor::STATUS_ACTIVE : Sensor::STATUS_DEACTIVATE,
            notify: $dto->notify === 'on',
        );
    }
}
