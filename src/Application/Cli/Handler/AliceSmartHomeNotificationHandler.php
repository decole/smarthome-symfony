<?php

namespace App\Application\Cli\Handler;

use App\Domain\Event\AlertNotificationEvent;
use App\Domain\Notification\Entity\AliceNotificationMessage;
use App\Infrastructure\Quasar\Service\QuasarNotificationService;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
final class AliceSmartHomeNotificationHandler
{
    public function __construct(
        private QuasarNotificationService $service,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function __invoke(AliceNotificationMessage $message): void
    {
        $template = [
            '-',
            ':',
        ];
        $preparedText = str_replace($template, '', $message->getMessage());

        $chunks = [];
        $textMap = explode(' ', $preparedText);

        $chunk = '';
        $space = 1;
        $i = 0;

        foreach ($textMap as $key => $word) {
            $lineCount = mb_strlen($chunk) + mb_strlen($word) + $space;

            if ($lineCount > 90) {
                $chunks[$i] = $chunk;
                $chunk = '';
                $i++;
            }

            $chunk = $chunk === '' ? $word : implode(' ', [$chunk, $word]);

            if (count($textMap) === $key + 1) {
                $chunks[$i] = $chunk;
            }
        }

        try {
            foreach ($chunks as $words) {
                $this->service->send($words);
                sleep(7);
            }
        } catch (Throwable $exception) {
            $this->eventDispatcher->dispatch(
                new AlertNotificationEvent($exception->getMessage(), [AlertNotificationEvent::MESSENGER]),
                AlertNotificationEvent::NAME
            );
        }

        sleep(1);
    }
}