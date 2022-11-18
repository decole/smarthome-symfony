<?php

namespace App\Tests\unit\Infrastructure\Doctrine\Service\Sensor;

use App\Domain\Doctrine\Sensor\Entity\SensorDryContact;
use App\Domain\Doctrine\Sensor\Entity\SensorHumidity;
use App\Domain\Doctrine\Sensor\Entity\SensorLeakage;
use App\Domain\Doctrine\Sensor\Entity\SensorPressure;
use App\Domain\Doctrine\Sensor\Entity\SensorTemperature;
use App\Infrastructure\Doctrine\Service\Sensor\SensorCrudService;
use App\Tests\UnitTester;
use Codeception\Example;
use Symfony\Component\HttpFoundation\Request;

class SensorCrudServiceCest
{
    public function getTypes(UnitTester $I): void
    {
        $service = $this->getService($I);

        $I->assertEquals([
            SensorTemperature::TYPE,
            SensorHumidity::TYPE,
            SensorLeakage::TYPE,
            SensorPressure::TYPE,
            SensorDryContact::TYPE,
        ], $service->getTypes());
    }

    /**
     * @param UnitTester $I
     * @param Example $example
     * @example(type="temperature")
     * @example(type="humidity")
     * @example(type="leakage")
     * @example(type="pressure")
     * @example(type="dryContact")
     * @return void
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

        if ($example['type'] === 'temperature' ||
            $example['type'] === 'humidity' ||
            $example['type'] === 'pressure'
        ) {
            $I->assertEquals(null, $dto->payloadMin);
            $I->assertEquals(null, $dto->payloadMax);
        }
        if ($example['type'] === 'leakage') {
            $I->assertEquals(null, $dto->payloadDry);
            $I->assertEquals(null, $dto->payloadWet);
        }
        if ($example['type'] === 'dryContact') {
            $I->assertEquals(null, $dto->payloadLow);
            $I->assertEquals(null, $dto->payloadHigh);
        }
    }

    /**
     * @param UnitTester $I
     * @param Example $example
     * @example(type="temperature")
     * @ example(type="humidity")
     * @ example(type="leakage")
     * @ example(type="pressure")
     * @ example(type="dryContact")
     * @return void
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

        if ($example['type'] === 'temperature' ||
            $example['type'] === 'humidity' ||
            $example['type'] === 'pressure'
        ) {
            $I->assertNotNull($dto->payloadMin);
            $I->assertNotNull($dto->payloadMax);
        }
        if ($example['type'] === 'leakage') {
            $I->assertNotNull($dto->payloadDry);
            $I->assertNotNull($dto->payloadWet);
        }
        if ($example['type'] === 'dryContact') {
            $I->assertNotNull($dto->payloadLow);
            $I->assertNotNull($dto->payloadHigh);
        }
    }

    private function getService(UnitTester $I): SensorCrudService
    {
        return $I->grabService(SensorCrudService::class);
    }

    /**
     * @param string $type
     * @param UnitTester $I
     * @return Request
     */
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
                $typeFields
            )
        );
    }
}