<?php

namespace App\Tests\functional\Application\Service\Validation\FireSecurity;

use App\Application\Service\Validation\FireSecurity\FireSecurityCrudValidationService;
use App\Tests\FunctionalTester;

class FireSecurityCrudValidationServiceCest
{
    // проверить что есть или нет токаго же датчика с таким же топиока
    public function validate(FunctionalTester $I): void
    {
        // создать сущность и провалидировать дто с таким же топиком
    }

    private function getService(FunctionalTester $I): FireSecurityCrudValidationService
    {
        return $I->grabService(FireSecurityCrudValidationService::class);
    }
}