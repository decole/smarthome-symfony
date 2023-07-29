<?php

namespace App\Tests\_support\Step\FunctionalStep\Domain\DeviceData\Service;

use App\Application\Http\Web\FireSecurity\Dto\CrudFireSecurityDto;
use App\Application\Http\Web\Relay\Dto\CrudRelayDto;
use App\Application\Http\Web\Security\Dto\CrudSecurityDto;
use App\Application\Http\Web\Sensor\Dto\CrudSensorDto;
use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\FireSecurity\Entity\FireSecurity;
use App\Domain\FireSecurity\Service\FireSecurityCrudService;
use App\Domain\Relay\Entity\Relay;
use App\Domain\Relay\Service\RelayCrudService;
use App\Domain\Security\Entity\Security;
use App\Domain\Security\Enum\SecurityStateEnum;
use App\Domain\Security\Service\SecurityCrudService;
use App\Domain\Sensor\Entity\Sensor;
use App\Domain\Sensor\Service\SensorCrudService;
use App\Tests\FunctionalTester;

class DeviceDataResolverStep extends FunctionalTester
{
    /**
     * @return Sensor[]
     */
    public function createAllTypeSensors(): array
    {
        $sensorList = [];
        $minMaxType = [
            'temperature',
            'humidity',
            'leakage',
            'pressure',
            'dryContact',
        ];

        foreach ($minMaxType as $type) {
            $sensorList[$type] = $this->createSensorByType($type);
        }

        return $sensorList;
    }

    public function createSensorByType(string $type): EntityInterface
    {
        $dto = new CrudSensorDto();

        $dto->type = $type;
        $dto->name = $this->faker()->word() . random_int(1, 1000) . $type;
        $dto->topic = $this->faker()->word() . random_int(1000, 2000);
        $dto->payload = $this->faker()->word();
        $dto->payloadMin = 0;
        $dto->payloadMax = 100;
        $dto->payloadDry = $dto->payloadLow = 0;
        $dto->payloadWet = $dto->payloadHigh = 1;
        $dto->message_info = $this->faker()->word();
        $dto->message_ok = $this->faker()->word();
        $dto->message_warn = $this->faker()->word();
        $dto->status = 'on';
        $dto->notify = 'on';

        return $this->crudSensorService()->create($dto);
    }

    public function createFireSecureDevice(): FireSecurity
    {
        $dto = new CrudFireSecurityDto();

        $dto->name = $this->faker()->word() . 'secure';
        $dto->topic = $this->faker()->word();
        $dto->payload = $this->faker()->word();
        $dto->normalPayload = $this->faker()->word();
        $dto->alertPayload = $this->faker()->word();
        $dto->lastCommand = $this->faker()->word();
        $dto->message_info = $this->faker()->word();
        $dto->message_ok = $this->faker()->word();
        $dto->message_warn = $this->faker()->word();
        $dto->status = 'on';
        $dto->notify = 'on';

        return $this->crudFireSecureService()->create($dto);
    }

    public function createDryRelayDevice(): Relay
    {
        $dto = new CrudRelayDto();

        $dto->type = 'relay';
        $dto->name = $this->faker()->word() . 'relay';
        $dto->topic = $this->faker()->word();
        $dto->payload = $this->faker()->word();
        $dto->commandOn = $this->faker()->word();
        $dto->commandOff = $this->faker()->word();
        $dto->isFeedbackPayload = $this->faker()->word();
        $dto->checkTopic = $this->faker()->word();
        $dto->checkTopicPayloadOn = $this->faker()->word();
        $dto->checkTopicPayloadOff = $this->faker()->word();
        $dto->lastCommand = $this->faker()->word();
        $dto->message_info = $this->faker()->word();
        $dto->message_ok = $this->faker()->word();
        $dto->message_warn = $this->faker()->word();
        $dto->status = 'on';
        $dto->notify = 'on';

        return $this->crudRelayService()->create($dto);
    }

    public function createSecurityDevice(): Security
    {
        $dto = new CrudSecurityDto();

        $dto->name = $this->faker()->word() . 'secure' . random_int(1,100) . random_int(1,100);
        $dto->topic = $this->faker()->word();
        $dto->payload = $this->faker()->word();
        $dto->detectPayload = $this->faker()->word();
        $dto->holdPayload = $this->faker()->word();
        $dto->lastCommand = SecurityStateEnum::HOLD_STATE->value;
        $dto->message_info = $this->faker()->word();
        $dto->message_ok = $this->faker()->word();
        $dto->message_warn = $this->faker()->word();
        $dto->status = 'on';
        $dto->notify = 'on';

        return $this->crudSecurityService()->create($dto);
    }

    public function crudSensorService(): SensorCrudService
    {
        return $this->grabService(SensorCrudService::class);
    }

    public function crudFireSecureService(): FireSecurityCrudService
    {
        return $this->grabService(FireSecurityCrudService::class);
    }

    public function crudRelayService(): RelayCrudService
    {
        return $this->grabService(RelayCrudService::class);
    }

    public function crudSecurityService(): SecurityCrudService
    {
        return $this->grabService(SecurityCrudService::class);
    }
}