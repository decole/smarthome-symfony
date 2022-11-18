<?php

namespace App\Infrastructure\Doctrine\Service\Sensor;

use App\Application\Helper\StringHelper;
use App\Application\Http\Web\Sensor\Dto\CrudSensorDto;
use App\Domain\Common\Embedded\StatusMessage;
use App\Domain\Contract\CrudValidation\ValidationDtoInterface;
use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\Sensor\Entity\DryContactSensor;
use App\Domain\Sensor\Entity\HumiditySensor;
use App\Domain\Sensor\Entity\LeakageSensor;
use App\Domain\Sensor\Entity\PressureSensor;
use App\Domain\Sensor\Entity\Sensor;
use App\Domain\Sensor\Entity\TemperatureSensor;
use App\Infrastructure\Doctrine\Service\Sensor\Exception\AdvancedFieldsException;
use App\Infrastructure\Doctrine\Service\Sensor\Factory\SensorCrudFactory;
use App\Infrastructure\Doctrine\Traits\CommonCrudFieldTraits;
use App\Infrastructure\Doctrine\Traits\StatusMessageTrait;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Webmozart\Assert\Assert;

final class SensorCrudService
{
    use StatusMessageTrait, CommonCrudFieldTraits;

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
        /** @var TemperatureSensor|HumiditySensor|PressureSensor|DryContactSensor|LeakageSensor $entity */
        $entity = $this->crud->getEntityById($id);
        $rc = new ReflectionClass($entity);

        $this->setDtoToEntityCommonParams($entity, $dto);

        if ($rc->hasMethod('getPayloadMin')) {
            $entity->setPayloadMin($dto->payloadMin);
            $entity->setPayloadMax($dto->payloadMax);
        }
        if ($rc->hasMethod('getPayloadDry')) {
            $entity->setPayloadDry($dto->payloadDry);
            $entity->setPayloadWet($dto->payloadWet);
        }
        if ($rc->hasMethod('getPayloadHigh')) {
            $entity->setPayloadHigh($dto->payloadHigh);
            $entity->setPayloadLow($dto->payloadLow);
        }

        $entity->setStatusMessage(new StatusMessage(
            $dto->message_info,
            $dto->message_ok,
            $dto->message_warn
        ));

        $entity->setStatus($dto->status === 'on' ? Sensor::STATUS_ACTIVE : Sensor::STATUS_DEACTIVATE);
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
        return array_keys(Sensor::DISCRIMINATOR_MAP);
    }

    public function createDto(string $type, ?Request $request): CrudSensorDto
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

    public function entityByDto(string $id): CrudSensorDto
    {
        /** @var TemperatureSensor|HumiditySensor|PressureSensor|DryContactSensor|LeakageSensor $entity */
        $entity = $this->crud->getEntityById($id);
        $rc = new ReflectionClass($entity);

        $dto = new CrudSensorDto();

        $this->setEntityToDtoCommonParams($dto, $entity);

        $dto->payloadMin = $rc->hasMethod('getPayloadMin') ? $entity?->getPayloadMin() : null;
        $dto->payloadMax = $rc->hasMethod('getPayloadMax') ? $entity?->getPayloadMax() : null;
        $dto->payloadDry = $rc->hasMethod('getPayloadDry') ? $entity?->getPayloadDry() : null;
        $dto->payloadWet = $rc->hasMethod('getPayloadWet') ? $entity?->getPayloadWet() : null;
        $dto->payloadHigh = $rc->hasMethod('getPayloadHigh') ? $entity?->getPayloadHigh() : null;
        $dto->payloadLow = $rc->hasMethod('getPayloadLow') ? $entity?->getPayloadLow() : null;

        $this->setStatusMessage($dto, $entity);

        $dto->status = $entity->getStatus() === Sensor::STATUS_ACTIVE ? 'on' : null;

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
     * @param CrudSensorDto $dto
     * @return array
     */
    private function getAdvancedFields(ValidationDtoInterface $dto): array
    {
        return match ($dto->type) {
            TemperatureSensor::TYPE, HumiditySensor::TYPE, PressureSensor::TYPE => [
                $dto->payloadMin,
                $dto->payloadMax,
            ],
            LeakageSensor::TYPE => [
                $dto->payloadDry,
                $dto->payloadWet,
            ],
            DryContactSensor::TYPE => [
                $dto->payloadHigh,
                $dto->payloadLow,
            ],
            default => AdvancedFieldsException::deviceTypeNotFound($dto->type),
        };
    }
}