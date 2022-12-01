<?php

namespace App\Tests\functional\Infrastructure\Cache;

use App\Infrastructure\Cache\CacheService;
use App\Tests\FunctionalTester;
use Symfony\Contracts\Cache\ItemInterface;

class CacheServiceCest
{
    private CacheService $cache;

    public function _before(FunctionalTester $I)
    {
        $this->cache = $I->grabService(CacheService::class);
    }

    public function get(FunctionalTester $I): void
    {
        $key = 'test-get-function';
        $value = $this->cache->get($key);

        $I->assertEquals(null, $value);
    }

    public function getOrSet(FunctionalTester $I): void
    {
        $key = 'test-get-or-set-function';
        $this->cache->delete([$key]);
        $fake = $I->faker()->word();

        $I->assertEquals(null, $this->cache->get($key));

        $value = $this->cache->getOrSet(
            key: $key,
            callback: function (ItemInterface $item) use ($fake) {
                return $fake;
            }
        );

        $I->assertEquals($fake, $value);
    }

    public function set(FunctionalTester $I): void
    {
        $key = 'test-get-or-set-function';
        $this->cache->delete([$key]);
        $value = $I->faker()->word();

        $I->assertEquals(null, $this->cache->get($key));

        $this->cache->set($key, $value);

        $I->assertEquals($value, $this->cache->get($key));
    }

    public function delete(FunctionalTester $I): void
    {
        $key = 'test-delete-function';
        $this->cache->set($key, $I->faker()->word());
        $this->cache->delete([$key]);

        $I->assertEquals(null, $this->cache->get($key));
    }

    public function clear(FunctionalTester $I): void
    {
        $keyOne = $I->faker()->word();
        $keyTwo = $I->faker()->word();
        $keyThree = $I->faker()->word();
        $value = $I->faker()->word();

        $this->cache->set($keyOne, $value);
        $this->cache->set($keyTwo, $value);
        $this->cache->set($keyThree, $value);

        $this->cache->clear();

        $I->assertEquals(null, $this->cache->get($keyOne));
        $I->assertEquals(null, $this->cache->get($keyTwo));
        $I->assertEquals(null, $this->cache->get($keyThree));
    }
}