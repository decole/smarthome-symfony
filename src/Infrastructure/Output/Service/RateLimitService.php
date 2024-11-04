<?php

declare(strict_types=1);

namespace App\Infrastructure\Output\Service;

use App\Infrastructure\Cache\CacheService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

final class RateLimitService
{
    private const CACHE_HTTP_KEY = 'http_limit';

    public function __construct(
        private readonly CacheService $cacheService,
        private readonly int $httpLimit,
        private readonly int $httpLimitMinutes
    ) {
    }

    public function http(Request $request): void
    {
        $context = new RateLimitContext(
            ip: $request->getClientIp(),
            cacheKey: self::CACHE_HTTP_KEY,
            limit: $this->httpLimit,
            minutes: $this->httpLimitMinutes
        );

        $this->consume($context);
    }

    private function consume(RateLimitContext $context): void
    {
        $count = (int)$this->cacheService->get($this->getClientKey($context));

        ++$count;

        if ($count > $context->limit) {
            throw new TooManyRequestsHttpException();
        }

        $this->cacheService->set($this->getClientKey($context), $count, $this->getLimitSeconds($context));
    }

    private function getClientKey(RateLimitContext $context): string
    {
        return sprintf('%s_%s', $context->cacheKey, $context->ip);
    }

    private function getLimitSeconds(RateLimitContext $context): int
    {
        return 60 * $context->minutes;
    }
}