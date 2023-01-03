<?php

namespace App\Infrastructure\Prometheus\Metric;

use Artprima\PrometheusMetricsBundle\Metrics\RequestMetricsCollectorInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMapping;
use Prometheus\CollectorRegistry;
use Prometheus\Exception\MetricsRegistrationException;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Throwable;

final class DataBaseMetricsCollector implements RequestMetricsCollectorInterface
{
    private string $namespace;

    private CollectorRegistry $collectionRegistry;

    public function __construct(private EntityManager $entityManager)
    {
    }

    public function init(string $namespace, CollectorRegistry $collectionRegistry): void
    {
        $this->namespace = $namespace;
        $this->collectionRegistry = $collectionRegistry;
    }

    /**
     * @throws MetricsRegistrationException
     */
    public function collectRequest(RequestEvent $event): void
    {
        $counter = $this->collectionRegistry->getOrRegisterCounter(
            $this->namespace,
            'notify_message_total',
            'notify message by notify stack count',
            ['count']
        );

        try {
            $counter->incBy($this->getCount(), ['count' => 'total']);
        } catch (Throwable $exception) {
            $counter->incBy(-1, ['count' => 'total']);
        }
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    private function getCount(): int
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('count', 'count', Types::BIGINT);
        $qb = $this->entityManager->createNativeQuery('select count(*) as count from messenger_messages', $rsm);

        return $qb->getSingleScalarResult();
    }
}