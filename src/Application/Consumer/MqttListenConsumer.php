<?php

namespace App\Application\Consumer;

use App\Domain\DeviceData\Service\DeviceDataResolver;
use App\Domain\Event\AlertNotificationEvent;
use App\Domain\Payload\Entity\DevicePayload;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

class MqttListenConsumer implements ConsumerInterface
{
    public function __construct(
        private readonly DeviceDataResolver $resolver,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly SerializerInterface $serializer,
        private readonly LoggerInterface $logger
    ) {
    }

    /** @deprecated */
    public function execute(AMQPMessage $msg): bool|int
    {
        try {
            /** @var DevicePayload $mqttMessageInput */
            $payload = $this->getDto($msg);

            $this->resolver->resolveDevicePayload($payload);

            return self::MSG_ACK;
        } catch (Throwable $exception) {
            $text = 'Ошибка в распознавании данных из брокера сообщений';
            $this->logger->info($text, [
                'exception' => $exception->getMessage(),
            ]);

            $event = new AlertNotificationEvent($text, [AlertNotificationEvent::MESSENGER]);
            $this->eventDispatcher->dispatch($event, AlertNotificationEvent::NAME);

            return self::MSG_REJECT;
        }
    }

    private function getDto(AMQPMessage $msg): DevicePayload
    {
        return $this->serializer->deserialize($msg->getBody(), type: DevicePayload::class, format: 'json');
    }
}