<?php

namespace App\Application\Consumer;

use App\Domain\Event\AlertNotificationEvent;
use App\Domain\Payload\Entity\DevicePayload;
use App\Infrastructure\Mqtt\Entity\MqttClientInterface;
use App\Infrastructure\Mqtt\Exception\MqttException;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

class MqttPublishConsumer implements ConsumerInterface
{
    public function __construct(
        private readonly MqttClientInterface $client,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly SerializerInterface $serializer,
        private readonly LoggerInterface $logger
    ) {
    }

    public function execute(AMQPMessage $msg)
    {
        try {
            /** @var DevicePayload $mqttMessageInput */
            $payload = $this->serializer->deserialize($msg->getBody(), type: DevicePayload::class, format: 'json');

            $this->client->publish($payload->getTopic(), $payload->getPayload());
        } catch (Throwable $exception) {
            $text = 'Не возможно отправить команду брокеру сообщений';

            $this->logger->critical($text, [
                'exception' => $exception->getMessage(),
            ]);

            $event = new AlertNotificationEvent($text, [
                AlertNotificationEvent::MESSENGER,
                AlertNotificationEvent::ALICE
            ]);

            $this->eventDispatcher->dispatch($event, AlertNotificationEvent::NAME);

            $this->client->disconnect();
        }
    }
}