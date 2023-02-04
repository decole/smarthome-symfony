<?php

namespace App\Tests\unit\Application\Service\SitePage;

use App\Application\Service\SitePage\ApiUriTranscribeService;
use App\Tests\UnitTester;

class ApiUriTranscribeServiceCest
{
    public function emptyUrl(UnitTester $I): void
    {
        /** @var ApiUriTranscribeService $service */
        $service = $I->grabService(ApiUriTranscribeService::class);

        $I->assertEquals([], $service->transcribeUri(''));
    }

    public function oneElementUrl(UnitTester $I): void
    {
        /** @var ApiUriTranscribeService $service */
        $service = $I->grabService(ApiUriTranscribeService::class);

        $I->assertEquals([0 => 'pom'], $service->transcribeUri('pom'));
    }

    public function manyElementUrl(UnitTester $I): void
    {
        /** @var ApiUriTranscribeService $service */
        $service = $I->grabService(ApiUriTranscribeService::class);

        $I->assertEquals(
            [
                0 => 'pom',
                1 => 'tom',
                2 => '1',
                3 => '2',
                4 => 'kek',
            ],
            $service->transcribeUri('pom,tom,1,2,kek')
        );
    }
}