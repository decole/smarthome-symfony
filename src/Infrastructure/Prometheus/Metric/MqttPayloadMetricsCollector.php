<?php

declare(strict_types=1);

namespace App\Infrastructure\Prometheus\Metric;

use App\Domain\DeviceData\Service\DeviceDataCacheService;
use Artprima\PrometheusMetricsBundle\Metrics\RequestMetricsCollectorInterface;
use Prometheus\CollectorRegistry;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final class MqttPayloadMetricsCollector implements RequestMetricsCollectorInterface
{
    private string $namespace;

    private CollectorRegistry $collectionRegistry;

    public function __construct(private DeviceDataCacheService $service)
    {
    }

    public function init(string $namespace, CollectorRegistry $collectionRegistry): void
    {
        $this->namespace = $namespace;
        $this->collectionRegistry = $collectionRegistry;
    }

    public function collectRequest(RequestEvent $event): void
    {
        foreach ($this->service->getList() as $topic => $payloadMap) {
            $payload = $payloadMap['payload'] ?? null;

            if ($payload === null) {
                continue;
            }

            if (filter_var($payload, FILTER_VALIDATE_FLOAT) !== false) {
                $this->incPayloads($topic, $payload);
            }
        }
    }

    private function incPayloads(string $topic, ?string $payload)
    {
        $counter = $this->collectionRegistry->getOrRegisterCounter(
            $this->namespace,
            'mqtt_payloads_total',
            'total mqtt payload by topic count',
            ['action']
        );

        $counter->inc(['all']);

        $gauge = $this->collectionRegistry->getOrRegisterGauge(
            $this->namespace,
            'mqtt_topic_value',
            'mqtt topic with payload',
            ['topic']
        );

        $gauge->set((float)$payload, ['topic' => $topic]);
    }
}