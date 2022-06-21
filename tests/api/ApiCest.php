<?php

namespace App\Tests\api;

use App\Tests\ApiTester;

class ApiCest
{
    public function createUserViaAPI(ApiTester $I): void
    {
        $I->amHttpAuthenticated('service_user', '123456');
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $I->sendPost('/users', [
            'name' => 'davert',
            'email' => 'davert@codeception.com'
        ]);
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseContains('{"result":"ok"}');
    }

    public function positiveGetPost(ApiTester $I): void
    {
        $response = $I->sendGet('/posts', [ 'status' => 'pending' ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
}