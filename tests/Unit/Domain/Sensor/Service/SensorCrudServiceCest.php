<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Sensor\Service;

use App\Domain\Sensor\Entity\DryContactSensor;
use App\Domain\Sensor\Entity\HumiditySensor;
use App\Domain\Sensor\Entity\LeakageSensor;
use App\Domain\Sensor\Entity\PressureSensor;
use App\Domain\Sensor\Entity\TemperatureSensor;
use App\Domain\Sensor\Service\SensorCrudService;
use App\Tests\Support\UnitTester;
use Codeception\Example;
use Symfony\Component\HttpFoundation\Request;

class SensorCrudServiceCest
{
    public function getTypes(UnitTester $I): void
    {
        $service = $this->getService($I);

        $I->assertEquals([
            TemperatureSensor::TYPE,
            HumiditySensor::TYPE,
            LeakageSensor::TYPE,
            PressureSensor::TYPE,
            DryContactSensor::TYPE,
        ], $service->getTypes());
    }

    /**
     * @example(type="temperature")
     * @example(type="humidity")
     * @example(type="leakage")
     * @example(type="pressure")
     * @example(type="dryContact")
     */
    public function createEmptyDto(UnitTester $I, Example $example): void
    {
        $service = $this->getService($I);

        $dto = $service->createDto($example['type'], null);

        $I->assertEquals($example['type'], $dto->type);
        $I->assertEquals(null, $dto->name);
        $I->assertEquals(null, $dto->topic);
        $I->assertEquals(null, $dto->payload);
        $I->assertEquals(null, $dto->message_info);
        $I->assertEquals(null, $dto->message_ok);
        $I->assertEquals(null, $dto->message_warn);
        $I->assertEquals(null, $dto->status);
        $I->assertEquals(null, $dto->notify);

        if ('temperature' === $example['type']
            || 'humidity' === $example['type']
            || 'pressure' === $example['type']
        ) {
            $I->assertEquals(null, $dto->payloadMin);
            $I->assertEquals(null, $dto->payloadMax);
        }
        if ('leakage' === $example['type']) {
            $I->assertEquals(null, $dto->payloadDry);
            $I->assertEquals(null, $dto->payloadWet);
        }
        if ('dryContact' === $example['type']) {
            $I->assertEquals(null, $dto->payloadLow);
            $I->assertEquals(null, $dto->payloadHigh);
        }
    }

    /**
     * @example(type="temperature")
     *
     * @ example(type="humidity")
     *
     * @ example(type="leakage")
     *
     * @ example(type="pressure")
     *
     * @ example(type="dryContact")
     */
    public function createByRequestDto(UnitTester $I, Example $example): void
    {
        $service = $this->getService($I);

        $request = $this->getRequest($example['type'], $I);

        $dto = $service->createDto($example['type'], $request);

        $I->assertEquals($example['type'], $dto->type);
        $I->assertNotNull($dto->name);
        $I->assertNotNull($dto->topic);
        $I->assertNotNull($dto->payload);
        $I->assertNotNull($dto->message_info);
        $I->assertNotNull($dto->message_ok);
        $I->assertNotNull($dto->message_warn);
        $I->assertNotNull($dto->status);
        $I->assertNotNull($dto->notify);

        if ('temperature' === $example['type']
            || 'humidity' === $example['type']
            || 'pressure' === $example['type']
        ) {
            $I->assertNotNull($dto->payloadMin);
            $I->assertNotNull($dto->payloadMax);
        }
        if ('leakage' === $example['type']) {
            $I->assertNotNull($dto->payloadDry);
            $I->assertNotNull($dto->payloadWet);
        }
        if ('dryContact' === $example['type']) {
            $I->assertNotNull($dto->payloadLow);
            $I->assertNotNull($dto->payloadHigh);
        }
    }

    private function getService(UnitTester $I): SensorCrudService
    {
        return $I->grabService(SensorCrudService::class);
    }

    private function getRequest(string $type, UnitTester $I): Request
    {
        $minMax = [
            'payloadMin' => $I->faker()->word(),
            'payloadMax' => $I->faker()->word(),
        ];
        $dryWet = [
            'payloadDry' => $I->faker()->word(),
            'payloadWet' => $I->faker()->word(),
        ];
        $lowHigh = [
            'payloadHigh' => $I->faker()->word(),
            'payloadLow' => $I->faker()->word(),
        ];

        $typeFields = match ($type) {
            'temperature', 'humidity', 'pressure' => $minMax,
            'leakage' => $dryWet,
            'dryContact' => $lowHigh,
        };

        return new Request(
            query: [],
            request: array_merge(
                [
                    'type' => $type,
                    'name' => $I->faker()->word(),
                    'topic' => $I->faker()->word(),
                    'payload' => $I->faker()->word(),
                    'message_info' => $I->faker()->word(),
                    'message_ok' => $I->faker()->word(),
                    'message_warn' => $I->faker()->word(),
                    'status' => $I->faker()->word(),
                    'notify' => $I->faker()->word(),
                ],
                $typeFields,
            ),
        );
    }
}
