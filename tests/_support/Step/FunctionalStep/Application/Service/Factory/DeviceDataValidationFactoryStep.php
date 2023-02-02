<?php

namespace App\Tests\_support\Step\FunctionalStep\Application\Service\Factory;

use App\Application\Http\Web\FireSecurity\Dto\CrudFireSecurityDto;
use App\Application\Http\Web\Relay\Dto\CrudRelayDto;
use App\Application\Http\Web\Security\Dto\CrudSecurityDto;
use App\Application\Http\Web\Sensor\Dto\CrudSensorDto;
use App\Domain\FireSecurity\Entity\FireSecurity;
use App\Domain\FireSecurity\Service\FireSecurityCrudService;
use App\Domain\Relay\Entity\Relay;
use App\Domain\Relay\Enum\RelayTypeEnum;
use App\Domain\Relay\Service\RelayCrudService;
use App\Domain\Security\Entity\Security;
use App\Domain\Security\Service\SecurityCrudService;
use App\Domain\Sensor\Entity\Sensor;
use App\Domain\Sensor\Service\SensorCrudService;
use App\Tests\FunctionalTester;

class DeviceDataValidationFactoryStep extends FunctionalTester
{
    public function createDeviceByTypes(): array
    {
        return [
            $this->getAnySensor(),
            $this->createRelay(),
            $this->createSecurity(),
            $this->createFireSecure(),
        ];
    }

    public function getAnySensor(): Sensor
    {
        $types = Sensor::SENSOR_TYPES;

        $type = $types[random_int(0, 4)];

        return $this->createSensorByType($type);
    }

    public function createSensorByType(string $type): Sensor
    {
        $dto = new CrudSensorDto();

        $dto->type = $type;
        $dto->name = $this->faker()->word();
        $dto->topic = $this->faker()->word();
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

        return $this->grabService(SensorCrudService::class)->create($dto);
    }

    public function createRelay(): Relay
    {
        $dto = new CrudRelayDto();

        $dto->type = RelayTypeEnum::DRY_RELAY_TYPE->value;
        $dto->name = $this->faker()->word();
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

        return $this->grabService(RelayCrudService::class)->create($dto);
    }

    private function createSecurity(): Security
    {
        $dto = new CrudSecurityDto();

        $dto->name = 'sec_' . $this->faker()->word();
        $dto->topic = 'sec_' . $this->faker()->word();
        $dto->payload = $this->faker()->word();
        $dto->detectPayload = $this->faker()->word();
        $dto->holdPayload = $this->faker()->word();
        $dto->lastCommand = $this->faker()->word();
        $dto->message_info = $this->faker()->word();
        $dto->message_ok = $this->faker()->word();
        $dto->message_warn = $this->faker()->word();
        $dto->status = 'on';
        $dto->notify = 'on';

        return $this->grabService(SecurityCrudService::class)->create($dto);
    }

    private function createFireSecure(): FireSecurity
    {
        $dto = new CrudFireSecurityDto();

        $dto->name = $this->faker()->word();
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

        return $this->grabService(FireSecurityCrudService::class)->create($dto);
    }
}