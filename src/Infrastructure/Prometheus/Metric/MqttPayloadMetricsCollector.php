<?php

declare(strict_types=1);

namespace App\Infrastructure\Prometheus\Metric;

use App\Domain\DeviceData\Service\DeviceCacheService;
use Artprima\PrometheusMetricsBundle\Metrics\RequestMetricsCollectorInterface;
use Prometheus\CollectorRegistry;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final class MqttPayloadMetricsCollector implements RequestMetricsCollectorInterface
{
    public function init(string $namespace, CollectorRegistry $collectionRegistry): void
    {
    }

    public function collectRequest(RequestEvent $event): void
    {
    }
}
