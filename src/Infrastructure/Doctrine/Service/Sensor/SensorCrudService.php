<?php


namespace App\Infrastructure\Doctrine\Service\Sensor;


use App\Application\Helper\StringHelper;
use App\Application\Http\Web\Sensor\Dto\CrudSensorDto;
use App\Application\Service\Validation\ValidationDtoInterface;
use App\Domain\Doctrine\DeviceCommon\Entity\StatusMessage;
use App\Domain\Doctrine\Sensor\Entity\Sensor;
use App\Domain\Doctrine\Sensor\Entity\SensorDryContact;
use App\Domain\Doctrine\Sensor\Entity\SensorHumidity;
use App\Domain\Doctrine\Sensor\Entity\SensorLeakage;
use App\Domain\Doctrine\Sensor\Entity\SensorPressure;
use App\Domain\Doctrine\Sensor\Entity\SensorTemperature;
use App\Infrastructure\Doctrine\Interfaces\EntityInterface;
use App\Infrastructure\Doctrine\Service\Sensor\Exception\AdvancedFieldsException;
use App\Infrastructure\Doctrine\Service\Sensor\Factory\SensorCrudFactory;
use App\Infrastructure\Doctrine\Traits\StatusMessageTrait;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Webmozart\Assert\Assert;

final class SensorCrudService
{
    use StatusMessageTrait;

    public function __construct(private SensorCrudFactory $crud)
    {
    }

    public function validate(CrudSensorDto $sensorDto, bool $isUpdate = false): ConstraintViolationListInterface
    {
        $this->crud->getValidationService()->setValue($sensorDto);

        return $this->crud->validate($isUpdate);
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    public function create(ValidationDtoInterface $dto): EntityInterface
    {
        assert($dto instanceof CrudSensorDto);

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
    public function update(string $id, CrudSensorDto $dto): EntityInterface
    {
        /** @var SensorTemperature|SensorHumidity|SensorPressure|SensorDryContact|SensorLeakage $entity */
        $entity = $this->crud->getRepository()->findById($id);
        $rc = new ReflectionClass($entity);

        $entity->setName($dto->name);
        $entity->setTopic($dto->topic);
        $entity->setPayload($dto->payload);

        if ($rc->hasMethod('getPayloadMin')) {
            $entity->setPayloadMin($dto->payload_min);
            $entity->setPayloadMax($dto->payload_max);
        }
        if ($rc->hasMethod('getPayloadDry')) {
            $entity->setPayloadDry($dto->payload_dry);
            $entity->setPayloadWet($dto->payload_wet);
        }
        if ($rc->hasMethod('getPayloadHigh')) {
            $entity->setPayloadHigh($dto->payload_high);
            $entity->setPayloadLow($dto->payload_low);
        }

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
        return array_keys(Sensor::DISCRIMINATOR_MAP);
    }

    public function createSensorDto(string $type, ?Request $request): CrudSensorDto
    {
        $dto = new CrudSensorDto();
        $dto->type = $type;

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
    public function entitySensorDto(string $id): CrudSensorDto
    {
        /** @var SensorTemperature|SensorHumidity|SensorPressure|SensorDryContact|SensorLeakage $entity */
        $entity = $this->crud->getRepository()->findById($id);
        $rc = new ReflectionClass($entity);

        $dto = new CrudSensorDto();
        $dto->type = $entity->getType();
        $dto->name = $entity->getName();
        $dto->topic = $entity->getTopic();
        $dto->payload = $entity->getPayload();

        $dto->payload_min = $rc->hasMethod('getPayloadMin') ? $entity?->getPayloadMin() : null;
        $dto->payload_max = $rc->hasMethod('getPayloadMax') ? $entity?->getPayloadMax() : null;
        $dto->payload_dry = $rc->hasMethod('getPayloadDry') ? $entity?->getPayloadDry() : null;
        $dto->payload_wet = $rc->hasMethod('getPayloadWet') ? $entity?->getPayloadWet() : null;
        $dto->payload_high = $rc->hasMethod('getPayloadHigh') ? $entity?->getPayloadHigh() : null;
        $dto->payload_low = $rc->hasMethod('getPayloadLow') ? $entity?->getPayloadLow() : null;

        $this->setStatusMessage($dto, $entity);

        $dto->status = $entity->getStatus() === Sensor::STATUS_ACTIVE ? 'on' : null;
        $dto->notify = $entity->isNotify() ? 'on' : null;

        return $dto;
    }

    /**
     * @param CrudSensorDto $dto
     * @return Sensor
     */
    public function getNewEntityByDto(CrudSensorDto $dto): Sensor
    {
        Assert::inArray($dto->type, $this->getTypes());

        $class = Sensor::DISCRIMINATOR_MAP[$dto->type];

        return new $class(
            $dto->name,
            $dto->topic,
            $dto->payload,
            new StatusMessage(
                $dto->message_info,
                $dto->message_ok,
                $dto->message_warn
            ),
            $dto->status === 'on' ? Sensor::STATUS_ACTIVE : Sensor::STATUS_DEACTIVATE,
            $dto->notify === 'on',
            ...$this->getAdvancedFields($dto)
        );
    }

    /**
     * Дополнительные уникальные поля разных типов сенсоров
     *
     * @param ValidationDtoInterface $dto
     * @return array
     */
    private function getAdvancedFields(ValidationDtoInterface $dto): array
    {
        return match ($dto->type) {
            SensorTemperature::TYPE, SensorHumidity::TYPE, SensorPressure::TYPE => [
                $dto->payload_min,
                $dto->payload_max,
            ],
            SensorLeakage::TYPE => [
                $dto->payload_dry,
                $dto->payload_wet,
            ],
            SensorDryContact::TYPE => [
                $dto->payload_high,
                $dto->payload_low,
            ],
            default => AdvancedFieldsException::deviceTypeNotFound($dto->type),
        };
    }
}