<?php

namespace App\Application\Cli\Handler;

use App\Domain\Notification\AliceNotification;
use App\Infrastructure\Quasar\Service\QuasarNotificationService;
use Decole\Quasar\Exception\ApiException;
use Decole\Quasar\Exception\RussianWordException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class AliceSmartHomeNotificationHandler
{
    public function __construct(private QuasarNotificationService $service)
    {
    }

    /**
     * @throws RussianWordException
     * @throws ApiException
     */
    public function __invoke(AliceNotification $message): void
    {
        $this->service->send($message->getMessage());
    }
}