<?php

declare(strict_types=1);

namespace App\Domain\PLC\Service;

use App\Application\Helper\StringHelper;
use App\Domain\Common\Embedded\StatusMessage;
use App\Domain\Common\Enum\EntityStatusEnum;
use App\Domain\DeviceData\Service\DeviceDataCacheService;
use App\Domain\Event\AlertNotificationEvent;
use App\Domain\Event\VisualNotificationEvent;
use App\Domain\PLC\Entity\PLC;
use Psr\Cache\CacheException;
use Psr\Cache\InvalidArgumentException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Throwable;

final class PlcHandleService
{
    private const PREFIX = 'plc_';
    private const NOTIFY_SUFFIX = 'notify_';
    private const DELAY_DAY = 86400;

    public function __construct(
        private readonly PlcCacheService $plcCache,
        private readonly DeviceDataCacheService $dataCacheService,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly LoggerInterface $logger
    ) {
    }

    public function execute(): void
    {
        try {
            $this->plcCache->create();

            while (true) {
                $this->handle();

                sleep(10);
            }
        } catch (Throwable $exception) {
            $this->logger->warning('Error handle process', [
                'exception' => $exception->getMessage(),
            ]);

            $this->eventDispatcher->dispatch(
                new AlertNotificationEvent($exception->getMessage(), [
                    AlertNotificationEvent::MESSENGER,
                ]),
                AlertNotificationEvent::NAME
            );
        }
    }

    private function handle(): void
    {
        $plcMap = $this->plcCache->getMap();

        $targetTopicList = array_map(static fn(array $raw) => $raw['topic'], $plcMap);

        $cachedTopicsWithPayload = $this->dataCacheService->getPayloadByTopicList($targetTopicList);

        foreach ($plcMap as $plc) {
            if ($cachedTopicsWithPayload[$plc['topic']] === null && !$this->validate($plc)) {
                $this->notifyOffline($plc);

                continue;
            }

            // если контроллер вышел на связь - удалям кэш
            if ($cachedTopicsWithPayload[$plc['topic']] !== null &&
                $this->plcCache->getCacheItem($this->getCacheTopicKey($plc['topic']))->get() !== null
            ) {
                $this->plcCache->set($this->getCacheTopicKey($plc['topic']), null, self::DELAY_DAY);
                $this->notifyOnline($plc);
            }
        }
    }

    private function getCacheTopicKey(string $topic): string
    {
        return StringHelper::cleanReservedCharacters(self::PREFIX . $topic);
    }

    private function getCacheNotifyKey(string $topic): string
    {
        return StringHelper::cleanReservedCharacters(self::PREFIX . self::NOTIFY_SUFFIX . $topic);
    }

    /**
     * @throws CacheException
     * @throws InvalidArgumentException
     */
    private function validate(array $plc): bool
    {
        $cacheKey = $this->getCacheTopicKey($plc['topic']);

        $time = $this->plcCache->getCacheItem($cacheKey)->get();

        if ($time === null) {
            $this->plcCache->set($cacheKey, time() + (int)$plc['delay'], self::DELAY_DAY);

            return true;
        }

        if (time() >= $time) {
            return false;
        }

        return true;
    }

    private function notifyOffline(array $plc): void
    {
        $notifyKey = $this->getCacheNotifyKey($plc['topic']);

        if ($this->plcCache->getCacheItem($notifyKey)->get() === null) {
            $this->plcCache->set($notifyKey, true, self::DELAY_DAY);

            $this->createEvents($plc['errorMessage']);
        }
    }

    private function notifyOnline(array $plc): void
    {
        $this->createEvents($plc['okMessage']);
    }

    private function createEvents(string $message): void
    {
        $this->eventDispatcher->dispatch(
            new AlertNotificationEvent($message, [
                AlertNotificationEvent::MESSENGER,
            ]),
            AlertNotificationEvent::NAME
        );

        $this->eventDispatcher->dispatch(
            new VisualNotificationEvent(
                $message,
                new PLC(
                    name: 'dummy',
                    targetTopic: 'dummy',
                    alarmSecondDelay: 0,
                    statusMessage: new StatusMessage(),
                    status: EntityStatusEnum::STATUS_ACTIVE->value,
                    notify: true
                )
            ),
            VisualNotificationEvent::NAME
        );
    }
}