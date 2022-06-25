<?php


namespace App\Infrastructure\Doctrine\Service\Relay;


use App\Application\Helper\StringHelper;
use App\Application\Http\Web\Relay\Dto\CrudRelayDto;
use App\Application\Service\Validation\ValidationDtoInterface;
use App\Domain\Doctrine\DeviceCommon\Entity\StatusMessage;
use App\Domain\Doctrine\Relay\Entity\Relay;
use App\Domain\Doctrine\Sensor\Entity\Sensor;
use App\Infrastructure\Doctrine\Interfaces\EntityInterface;
use App\Infrastructure\Doctrine\Service\Relay\Factory\RelayCrudFactory;
use App\Infrastructure\Doctrine\Traits\StatusMessageTrait;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Webmozart\Assert\Assert;

final class RelayCrudService
{
    use StatusMessageTrait;

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
        $entity = $this->crud->getRepository()->findById($id);

        assert($entity instanceof Relay);

        $entity->setType($dto->type);
        $entity->setName($dto->name);
        $entity->setTopic($dto->topic);
        $entity->setPayload($dto->payload);
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

        $entity->setStatus($dto->status === 'on' ? Sensor::STATUS_ACTIVE : Sensor::STATUS_DEACTIVATE);
        $entity->setNotify($dto->notify === 'on');

        return $this->crud->save($entity);
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    public function delete(string $id): void
    {
        $entity = $this->crud->getRepository()->findById($id);

        if ($entity) {
            $this->crud->delete($entity);
        }
    }

    public function getTypes(): array
    {
        return Relay::RELAY_TYPES;
    }

    public function createRelayDto(?Request $request): CrudRelayDto
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

    /**
     * @throws NonUniqueResultException
     */
    public function entityRelayDto(string $id): CrudRelayDto
    {
        $entity = $this->crud->getRepository()->findById($id);

        assert($entity instanceof Relay);

        $dto = new CrudRelayDto();
        $dto->type = $entity->getType();
        $dto->name = $entity->getName();
        $dto->topic = $entity->getTopic();
        $dto->payload = $entity->getPayload();
        $dto->commandOn = $entity->getCommandOn();
        $dto->commandOff = $entity->getCommandOff();
        $dto->lastCommand = $entity->getLastCommand();

        $dto->isFeedbackPayload = $entity->isFeedbackPayload() === true ? 'on' : null;
        $dto->checkTopic = $entity->getCheckTopic();
        $dto->checkTopicPayloadOn = $entity->getCheckTopicPayloadOn();
        $dto->checkTopicPayloadOff = $entity->getCheckTopicPayloadOff();

        $this->setStatusMessage($dto, $entity);

        $dto->status = $entity->getStatus() === Sensor::STATUS_ACTIVE ? 'on' : null;
        $dto->notify = $entity->isNotify() ? 'on' : null;

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