<?php

namespace App\Infrastructure\Quasar\Service;

use App\Domain\Event\AlertNotificationEvent;
use Decole\Quasar\Exception\ApiException;
use Decole\Quasar\Exception\RussianWordException;
use Decole\Quasar\QuasarClient;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Throwable;

final class QuasarNotificationService
{
    private QuasarClient $simpleClient;

    private QuasarClient $advancedClient;

    /**
     * @throws RussianWordException
     */
    public function __construct(
        string $cookies,
        string $deviceId,
        string $scenarioId,
        private LoggerInterface $logger,
        private EventDispatcherInterface $eventDispatcher
    ) {
        $this->simpleClient = new QuasarClient($cookies);
        $this->advancedClient = new QuasarClient($cookies, 'Голос', $deviceId, $scenarioId);
    }

    /**
     * Отправка в https://yandex.ru/quasar/iot/ в сценарий "Голос" требуемый текст и его озвучка
     */
    public function send(string $message): void
    {
        try {
            $this->advancedClient->changeTextSpeechByScenario($message);
            $this->advancedClient->executeSpeechByScenario();
        } catch (Throwable $exception) {
            $this->logger->critical('Can`t send quasar notify message', [
                'exception' => $exception->getMessage(),
            ]);

            $event = new AlertNotificationEvent(
                'Can`t send quasar notify message ' . $exception->getMessage(),
                [AlertNotificationEvent::MESSENGER]
            );
            $this->eventDispatcher->dispatch($event, AlertNotificationEvent::NAME);
        }
    }

    public function getDevices(): array
    {
        return $this->simpleClient->getDevices();
    }

    /**
     * @throws RussianWordException
     * @throws ApiException
     */
    public function setScenario(): string
    {
        return $this->simpleClient->createScenario();
    }

    /**
     * @throws ApiException
     */
    public function deleteScenario(): bool
    {
        return $this->advancedClient->deleteScenario();
    }
}