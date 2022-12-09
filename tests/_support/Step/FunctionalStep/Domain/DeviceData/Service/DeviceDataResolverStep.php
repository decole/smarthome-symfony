<?php

namespace App\Tests\_support\Step\FunctionalStep\Domain\DeviceData\Service;

use App\Application\Http\Web\FireSecurity\Dto\CrudFireSecurityDto;
use App\Application\Http\Web\Relay\Dto\CrudRelayDto;
use App\Application\Http\Web\Sensor\Dto\CrudSensorDto;
use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\FireSecurity\Entity\FireSecurity;
use App\Domain\Relay\Entity\Relay;
use App\Infrastructure\Doctrine\Service\FireSecurity\FireSecurityCrudService;
use App\Infrastructure\Doctrine\Service\Relay\RelayCrudService;
use App\Infrastructure\Doctrine\Service\Sensor\SensorCrudService;
use App\Tests\FunctionalTester;

class DeviceDataResolverStep extends FunctionalTester
{
    /**
     * @param FunctionalTester $I
     * @return EntityInterface[]
     */
    public function createAllTypeSensors(FunctionalTester $I): array
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
            $sensorList[$type] = $this->createSensorByType($I, $type);
        }

        return $sensorList;
    }

    public function createSensorByType(FunctionalTester $I, string $type): EntityInterface
    {
        $dto = new CrudSensorDto();

        $dto->type = $type;
        $dto->name = $I->faker()->word();
        $dto->topic = $I->faker()->word();
        $dto->payload = $I->faker()->word();
        $dto->payloadMin = 0;
        $dto->payloadMax = 100;
        $dto->payloadDry = $dto->payloadLow = 0;
        $dto->payloadWet = $dto->payloadHigh = 1;
        $dto->message_info = $I->faker()->word();
        $dto->message_ok = $I->faker()->word();
        $dto->message_warn = $I->faker()->word();
        $dto->status = 'on';
        $dto->notify = 'on';

        return $this->crudSensorService($I)->create($dto);
    }

    public function createFireSecureDevice(DeviceDataResolverStep $I): FireSecurity
    {
        $dto = new CrudFireSecurityDto();

        $dto->name = $I->faker()->word();
        $dto->topic = $I->faker()->word();
        $dto->payload = $I->faker()->word();
        $dto->normalPayload = $I->faker()->word();
        $dto->alertPayload = $I->faker()->word();
        $dto->lastCommand = $I->faker()->word();
        $dto->message_info = $I->faker()->word();
        $dto->message_ok = $I->faker()->word();
        $dto->message_warn = $I->faker()->word();
        $dto->status = 'on';
        $dto->notify = 'on';

        return $this->crudFireSecureService($I)->create($dto);
    }

    public function createDryRelayDevice(DeviceDataResolverStep $I): Relay
    {
        $dto = new CrudRelayDto();

        $dto->type = 'relay';
        $dto->name = $I->faker()->word();
        $dto->topic = $I->faker()->word();
        $dto->payload = $I->faker()->word();
        $dto->commandOn = $I->faker()->word();
        $dto->commandOff = $I->faker()->word();
        $dto->isFeedbackPayload = $I->faker()->word();
        $dto->checkTopic = $I->faker()->word();
        $dto->checkTopicPayloadOn = $I->faker()->word();
        $dto->checkTopicPayloadOff = $I->faker()->word();
        $dto->lastCommand = $I->faker()->word();
        $dto->message_info = $I->faker()->word();
        $dto->message_ok = $I->faker()->word();
        $dto->message_warn = $I->faker()->word();
        $dto->status = 'on';
        $dto->notify = 'on';

        return $this->crudRelayService($I)->create($dto);
    }

    private function crudSensorService(FunctionalTester $I): SensorCrudService
    {
        return $I->grabService(SensorCrudService::class);
    }

    private function crudFireSecureService(FunctionalTester $I): FireSecurityCrudService
    {
        return $I->grabService(FireSecurityCrudService::class);
    }

    private function crudRelayService(FunctionalTester $I): RelayCrudService
    {
        return $I->grabService(RelayCrudService::class);
    }
}