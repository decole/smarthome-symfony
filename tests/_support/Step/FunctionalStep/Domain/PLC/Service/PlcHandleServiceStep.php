<?php

namespace App\Tests\_support\Step\FunctionalStep\Domain\PLC\Service;

use App\Application\Http\Web\Plc\Dto\CrudPlcDto;
use App\Application\Http\Web\Sensor\Dto\CrudSensorDto;
use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\PLC\Entity\PLC;
use App\Domain\PLC\Service\PlcCrudService;
use App\Domain\Sensor\Entity\Sensor;
use App\Infrastructure\Doctrine\Service\Sensor\SensorCrudService;
use App\Tests\FunctionalTester;

class PlcHandleServiceStep extends FunctionalTester
{
    public function createSensor(): EntityInterface
    {
        $dto = new CrudSensorDto();

        $dto->type = 'temperature';
        $dto->name = $this->faker()->word() . '_plc';
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

        return $this->crudSensorService()->create($dto);
    }

    public function savePlc(string $name, string $topic, int $delay, bool $status = true): PLC
    {
        $dto = new CrudPlcDto();

        $dto->name = $name;
        $dto->targetTopic = $topic;
        $dto->alarmSecondDelay = $delay;
        $dto->message_info = $dto->name . ' info';
        $dto->message_ok = $dto->name . ' ok';
        $dto->message_warn = $dto->name . ' warning';
        $dto->status = $status === true ? 'on' : null;
        $dto->notify = $status === true ? 'on' : null;

        return $this->crudPlcService()->create($dto);
    }

    public function crudSensorService(): SensorCrudService
    {
        return $this->grabService(SensorCrudService::class);
    }

    public function crudPlcService(): PlcCrudService
    {
        return $this->grabService(PlcCrudService::class);
    }
}