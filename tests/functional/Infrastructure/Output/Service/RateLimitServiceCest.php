<?php

namespace App\Tests\functional\Infrastructure\Output\Service;

use App\Infrastructure\Cache\CacheService;
use App\Infrastructure\Output\Service\RateLimitService;
use App\Tests\FunctionalTester;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class RateLimitServiceCest
{
    private CacheService $cache;

    private const KEY = 'http_limit';
    private const IP = '131.234.12.8';

    public function _before(FunctionalTester $I): void
    {
        $this->cache = $I->grabService(CacheService::class);
    }

    public function positiveRequest(FunctionalTester $I): void
    {
        $request = $this->getRequest();

        $service = new RateLimitService($this->cache, 1, 1);

        $service->http($request);

        $count = $this->cache->get($this->getKey());

        $I->assertEquals(1, $count);

        $this->cache->set($this->getKey(), null);
    }

    public function negativeLimit(FunctionalTester $I): void
    {
        $this->cache->set($this->getKey(), 2);

        $request = $this->getRequest();

        $service = new RateLimitService($this->cache, 2, 1);

        $I->expectThrowable(TooManyRequestsHttpException::class, fn() => $service->http($request));

        $this->cache->set($this->getKey(), null);
    }

    public function positiveInCount(FunctionalTester $I): void
    {
        $this->cache->set($this->getKey(), 1);

        $request = $this->getRequest();

        $service = new RateLimitService($this->cache, 2, 1);

        $service->http($request);

        $count = $this->cache->get($this->getKey());

        $I->assertEquals(2, $count);

        $this->cache->set($this->getKey(), null);
    }

    public function positiveInCountWithNullCount(FunctionalTester $I): void
    {
        $this->cache->set($this->getKey(), null);

        $request = $this->getRequest();

        $service = new RateLimitService($this->cache, 2, 1);

        $service->http($request);

        $count = $this->cache->get($this->getKey());

        $I->assertEquals(1, $count);

        $this->cache->set($this->getKey(), null);
    }

    private function getRequest(): Request
    {
        return new Request([], [], [], [], [], [
            'REMOTE_ADDR' => self::IP,
        ]);
    }

    private function getKey(): string
    {
        return self::KEY . '_' . self::IP;
    }
}