<?php

namespace App\Tests\functional\Domain\PLC\Service;

use App\Domain\DeviceData\Service\DeviceDataCacheService;
use App\Domain\Payload\Entity\DevicePayload;
use App\Domain\PLC\Service\PlcCacheService;
use App\Domain\PLC\Service\PlcHandleService;
use App\Domain\Sensor\Entity\Sensor;
use App\Infrastructure\Cache\CacheKeyListEnum;
use App\Infrastructure\Cache\CacheService;
use App\Tests\_support\Step\FunctionalStep\Domain\PLC\Service\PlcHandleServiceStep;
use Codeception\Stub;
use Codeception\Stub\Expected;
use Psr\Log\NullLogger;
use ReflectionMethod;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PlcHandleServiceCest
{
    private ?Sensor $device = null;
    private PlcCacheService $plcCache;
    private DeviceDataCacheService $dataCacheService;
    private CacheService $cache;

    public function _before(PlcHandleServiceStep $I): void
    {
        if ($this->device === null) {
            $this->device = $I->createSensor();
        }

        $this->plcCache = $I->grabService(PlcCacheService::class);
        $this->dataCacheService = $I->grabService(DeviceDataCacheService::class);
        $this->cache = $I->grabService(CacheService::class);
    }

    public function positiveCheckHandleData(PlcHandleServiceStep $I): void
    {
        $plc = $I->savePlc(
            name: $plcName = $I->faker()->word(),
            topic: $plcTopic = $this->device->getTopic(),
            delay: $plcDelay = 60
        );

        $this->dataCacheService->save(new DevicePayload(
            topic: $this->device->getTopic(),
            payload: $I->faker()->word()
        ));

        $event = Stub::makeEmpty(EventDispatcherInterface::class, ['dispatch' => Expected::never()]);
        $service = $this->getService($event);

        $reflection = new ReflectionMethod($service, 'handle');
        $reflection->setAccessible(true);
        $reflection->invoke($service);

        $map = $this->getCachePlcMap();
        $I->assertNotNull($map);
        $I->assertEquals([$plcTopic], array_keys($map));
        $I->assertEquals($plcTopic, $map[$this->device->getTopic()]['topic'] ?? []);
        $I->assertEquals($plcDelay, $map[$this->device->getTopic()]['delay'] ?? []);
        $I->assertEquals($plcName . ' ok', $map[$this->device->getTopic()]['okMessage'] ?? []);
        $I->assertEquals($plcName . ' warning', $map[$this->device->getTopic()]['errorMessage'] ?? []);
        $I->assertEquals(true, $map[$this->device->getTopic()]['isNotify'] ?? []);

        $I->assertEquals($plcName, $plc->getName());
        $I->assertEquals($plcTopic, $plc->getTargetTopic());
        $I->assertEquals($plcDelay, $plc->getAlarmSecondDelay());
        $I->assertEquals($plcName . ' info', $plc->getStatusMessage()->getMessageInfo());
        $I->assertEquals($plcName . ' ok', $plc->getStatusMessage()->getMessageOk());
        $I->assertEquals($plcName . ' warning', $plc->getStatusMessage()->getMessageWarn());
        $I->assertEquals(true, $plc->isNotify());
    }

    public function onlineDeviceAndEmptyPlc(PlcHandleServiceStep $I): void
    {
        $this->emptyCachePlcMap();

        $this->dataCacheService->save(new DevicePayload(
            topic: $this->device->getTopic(),
            payload: $I->faker()->word()
        ));

        $event = Stub::makeEmpty(EventDispatcherInterface::class, ['dispatch' => Expected::never()]);
        $service = $this->getService($event);

        $reflection = new ReflectionMethod($service, 'handle');
        $reflection->setAccessible(true);
        $reflection->invoke($service);

        $map = $this->getCachePlcMap();
        $I->assertNotNull($map);
        $I->assertEquals([], $map);
    }

    /**
     * Контроллер уже оффлайн, первый тик валидации, создание кэш ключа с задержкой выставленной в настройках PLC.
     * Еще время не пришло и нотификации не произведены.
     * @param PlcHandleServiceStep $I
     * @return void
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \ReflectionException
     */
    public function offlineDeviceAndFirstDetectOfflinePlc(PlcHandleServiceStep $I): void
    {
        $plc = $I->savePlc(
            name: $plcName = $I->faker()->word(),
            topic: $plcTopic = $this->device->getTopic(),
            delay: $plcDelay = 60
        );

        $this->dataCacheEmpty();

        $event = Stub::makeEmpty(EventDispatcherInterface::class, ['dispatch' => Expected::never()]);
        $service = $this->getService($event);

        $reflection = new ReflectionMethod($service, 'handle');
        $reflection->setAccessible(true);
        $reflection->invoke($service);

        $reflectionCache = new ReflectionMethod($service, 'getCacheTopicKey');
        $reflectionCache->setAccessible(true);
        $cacheKey = $reflectionCache->invoke($service, $plcTopic);

        $map = $this->getCachePlcMap();
        $I->assertNotNull($map);
        $I->assertEquals([$plcTopic], array_keys($map));
        $I->assertEquals('plc_'.$this->device->getTopic(), $cacheKey);
        $I->assertIsInt($this->cache->get($cacheKey));
    }

    /**
     * Контроллер уже оффлайн, второй тик валидации, есть кэш ключа с задержкой выставленной в настройках PLC.
     * Пришло время и нужно нотификацировать.
     * @param PlcHandleServiceStep $I
     * @return void
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \ReflectionException
     */
    public function offlineDeviceAndFirstDetectOfflinePlcWithNotify(PlcHandleServiceStep $I): void
    {
        $plc = $I->savePlc(
            name: $plcName = $I->faker()->word(),
            topic: $plcTopic = $this->device->getTopic(),
            delay: $plcDelay = 60
        );

        $this->dataCacheEmpty();
        $this->cache->set('plc_'.$this->device->getTopic(), time(), 60);

        $event = Stub::makeEmpty(
            EventDispatcherInterface::class,
            ['dispatch' => Expected::exactly(2, fn () => (object)[])]
        );
        $service = $this->getService($event);

        $reflection = new ReflectionMethod($service, 'handle');
        $reflection->setAccessible(true);
        $reflection->invoke($service);

        $reflectionCache = new ReflectionMethod($service, 'getCacheTopicKey');
        $reflectionCache->setAccessible(true);
        $cacheKey = $reflectionCache->invoke($service, $plcTopic);

        $reflectionNotifyCache = new ReflectionMethod($service, 'getCacheNotifyKey');
        $reflectionNotifyCache->setAccessible(true);
        $cacheNotifyKey = $reflectionNotifyCache->invoke($service, $plcTopic);

        $map = $this->getCachePlcMap();
        $I->assertNotNull($map);
        $I->assertEquals([$plcTopic], array_keys($map));
        $I->assertEquals('plc_'.$this->device->getTopic(), $cacheKey);
        $I->assertIsInt($this->cache->get($cacheKey));
        $I->assertEquals(true, $this->cache->get($cacheNotifyKey));
    }

    public function checkNotifyOfflineSendWithoutCacheNotifyMessage(PlcHandleServiceStep $I): void
    {
        $plc = [
            'controllerName' => $I->faker()->word(),
            'topic' => $I->faker()->word(),
            'delay' => 60,
            'okMessage' => $I->faker()->word(),
            'errorMessage'  => $I->faker()->word(),
            'isNotify' => true,
        ];

        $event = Stub::makeEmpty(
            EventDispatcherInterface::class,
            ['dispatch' => Expected::exactly(2, fn () => (object)[])]
        );
        $service = $this->getService($event);

        $reflectionNotifyCache = new ReflectionMethod($service, 'getCacheNotifyKey');
        $reflectionNotifyCache->setAccessible(true);
        $cacheNotifyKey = $reflectionNotifyCache->invoke($service, $plc['topic']);

        $I->assertEquals(null, $this->cache->get($cacheNotifyKey));

        // инициализируем первый раз вызов нотификаций
        $reflectionHydrate = new ReflectionMethod($service, 'notifyOffline');
        $reflectionHydrate->setAccessible(true);
        $reflectionHydrate->invoke($service, $plc);

        $I->assertEquals(true, $this->cache->get($cacheNotifyKey));


        // инициализируем второй раз вызов нотификаций, оповещения не произойдет
        $event = Stub::makeEmpty(EventDispatcherInterface::class, ['dispatch' => Expected::never()]);
        $service = $this->getService($event);

        $reflectionHydrate = new ReflectionMethod($service, 'notifyOffline');
        $reflectionHydrate->setAccessible(true);
        $reflectionHydrate->invoke($service, $plc);

        $I->assertEquals(true, $this->cache->get($cacheNotifyKey));
    }

    public function checkNotifyOnlineSendWithoutCacheNotifyMessage(PlcHandleServiceStep $I): void
    {
        $plc = [
            'controllerName' => $I->faker()->word(),
            'topic' => $I->faker()->word(),
            'delay' => 60,
            'okMessage' => $I->faker()->word(),
            'errorMessage'  => $I->faker()->word(),
            'isNotify' => true,
        ];

        $event = Stub::makeEmpty(
            EventDispatcherInterface::class,
            ['dispatch' => Expected::exactly(2, fn () => (object)[])]
        );
        $service = $this->getService($event);

        $reflectionHydrate = new ReflectionMethod($service, 'notifyOnline');
        $reflectionHydrate->setAccessible(true);
        $reflectionHydrate->invoke($service, $plc);



        $event = Stub::makeEmpty(
            EventDispatcherInterface::class,
            ['dispatch' => Expected::exactly(2, fn () => (object)[])]
        );
        $service = $this->getService($event);

        $reflectionHydrate = new ReflectionMethod($service, 'notifyOnline');
        $reflectionHydrate->setAccessible(true);
        $reflectionHydrate->invoke($service, $plc);
    }

    private function getService(EventDispatcherInterface $event): PlcHandleService
    {
        return new PlcHandleService(
            plcCache: $this->plcCache,
            dataCacheService: $this->dataCacheService,
            eventDispatcher: $event,
            logger: new NullLogger()
        );
    }

    private function getCachePlcMap(): mixed
    {
        return $this->cache->get('plc_cache_map');
    }

    private function clearCachePlcMap(): void
    {
        $this->cache->delete(['plc_cache_map']);
    }

    private function emptyCachePlcMap(): void
    {
        $this->cache->set('plc_cache_map', [], 30);
    }

    private function dataCacheEmpty(): void
    {
        $this->cache->set(CacheKeyListEnum::DEVICE_TOPICS_LIST->value, [], 30);
    }
}