<?php

namespace App\Tests\_support\Step\FunctionalStep\Domain\DeviceData\Service;

use App\Application\Http\Web\Sensor\Dto\CrudSensorDto;
use App\Domain\Contract\Repository\EntityInterface;
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

        return $this->crudService($I)->create($dto);
    }

    private function crudService(FunctionalTester $I): SensorCrudService
    {
        return $I->grabService(SensorCrudService::class);
    }
}