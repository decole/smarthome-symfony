<?php

namespace App\Domain\Security\EventListener;

use App\Domain\Payload\Entity\DevicePayload;
use App\Domain\Security\Event\MqttSecurityAlertEvent;
use App\Infrastructure\Integrator\HttpApi\ApiIntegrator;
use App\Infrastructure\Mqtt\Service\MqttHandleService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: MqttSecurityAlertEvent::NAME, method: 'onSecurityAlert')]
final class MqttSecurityAlertEventListener
{
    public function __construct(
        private MqttHandleService $service,
    ) {
    }

    public function onSecurityAlert(MqttSecurityAlertEvent $event): void
    {
        $this->hydrate($event->getDevice()->getParams());

        if ($event->getDevice()->getParams() === []) {
            return;
        }

        $this->hydrate($event->getDevice()->getParams());
    }

    private function hydrate(array $raw): void
    {
        if (array_key_exists('mqtt', $raw)) {
            $mqtt = $raw['mqtt'];

            if (array_key_exists('publishTopic', $mqtt) && array_key_exists('payload', $mqtt)) {
                $this->service->post(new DevicePayload(topic: $mqtt['publishTopic'], payload: $mqtt['payload']));
            }
        }

        if (array_key_exists('api', $raw)) {
            $api = $raw['api'];

            if (array_key_exists('entrypoint', $api) &&
                array_key_exists('method', $api) &&
                ($api['method'] === 'get' || $api['method'] === 'post')
            ) {
                $url = $api['entrypoint'];
                $method = $api['method'];
                $options = isset($api['body']) ? ['body' => $api['body']] : [];

                (new ApiIntegrator())->$method($url, $options);
            }
        }
    }
}