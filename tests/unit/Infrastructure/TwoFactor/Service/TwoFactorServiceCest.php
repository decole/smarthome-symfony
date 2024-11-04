<?php

namespace App\Tests\unit\Infrastructure\TwoFactor\Service;

use App\Tests\_support\Step\UnitStep\Infrastructure\TwoFactor\TwoFactorServiceStep;
use App\Infrastructure\TwoFactor\Service\TwoFactorService;
use Codeception\Stub;

class TwoFactorServiceCest
{
    public function positiveCheckIsEnable(TwoFactorServiceStep $I): void
    {
        $service = new TwoFactorService(true);

        $I->assertEquals(true, $service->isEnabled());
    }

    public function negativeCheckIsEnable(TwoFactorServiceStep $I): void
    {
        $service = new TwoFactorService(false);

        $I->assertEquals(false, $service->isEnabled());
    }

    public function getTemporarySecretWithEmptyCode(TwoFactorServiceStep $I): void
    {
        $request = $I->getRequest();
        $service = new TwoFactorService(true);

        $I->assertEquals(null, $request->getSession()->get(TwoFactorService::TEMPORARY_KEY));

        $secret = $service->getTemporarySecret($request);

        $I->assertEquals($secret, $request->getSession()->get(TwoFactorService::TEMPORARY_KEY));
    }

    public function getTemporarySecretWithCode(TwoFactorServiceStep $I): void
    {
        $request = $I->getRequest();
        $service = new TwoFactorService(true);

        $request->getSession()->set(TwoFactorService::TEMPORARY_KEY, $secret = $I->faker()->word());
        $serviceSecret = $service->getTemporarySecret($request);

        $I->assertEquals($secret, $serviceSecret);
    }

    public function validateCodeByEmptyCode(TwoFactorServiceStep $I): void
    {
        $service = new TwoFactorService(true);

        $I->assertEquals(false, $service->validateCode($I->faker()->word(), null));
    }

    public function positiveConfirm(TwoFactorServiceStep $I): void
    {
        $user = $I->getUser();
        $user->setAuthSecret($I->faker()->word());
        $key = md5($user->getTwoFactorCode());

        $request = $I->getRequest();
        $request->getSession()->set(TwoFactorService::NAME, $key);

        $service = new TwoFactorService(true);

        $I->assertEquals(true, $service->isConfirm($user, $request));
    }

    public function negativeConfirm(TwoFactorServiceStep $I): void
    {
        $user = $I->getUser();
        $user->setAuthSecret($I->faker()->word());
        $key = $I->faker()->word();

        $request = $I->getRequest();
        $request->getSession()->set(TwoFactorService::NAME, $key);

        $service = new TwoFactorService(true);

        $I->assertEquals(false, $service->isConfirm($user, $request));
    }

    public function deleteSessionVerifiedStateHasSessionKey(TwoFactorServiceStep $I): void
    {
        $user = $I->getUser();
        $user->setAuthSecret($secret = $I->faker()->word());
        $request = $I->getRequest();
        $service = new TwoFactorService(true);
        $service->setSessionIsVerifiedState($user, $request);

        $service->deleteSessionVerifiedState($request);

        $key = $request->getSession()->get(TwoFactorService::NAME);

        $I->assertEquals(null, $key);
    }

    public function deleteSessionVerifiedStateWithEmpty(TwoFactorServiceStep $I): void
    {
        $user = $I->getUser();
        $user->setAuthSecret($secret = $I->faker()->word());
        $request = $I->getRequest();
        $service = new TwoFactorService(true);

        $service->deleteSessionVerifiedState($request);

        $key = $request->getSession()->get(TwoFactorService::NAME);

        $I->assertEquals(null, $key);
    }

    public function setSessionIsVerifiedState(TwoFactorServiceStep $I): void
    {
        $user = $I->getUser();
        $user->setAuthSecret($secret = $I->faker()->word());
        $request = $I->getRequest();
        $service = new TwoFactorService(true);
        $service->setSessionIsVerifiedState($user, $request);

        $key = $request->getSession()->get(TwoFactorService::NAME);

        $I->assertEquals(md5($secret), $key);
    }

    public function positiveCheckCode(TwoFactorServiceStep $I): void
    {
        $user = $I->getUser();
        $code = $I->faker()->word();
        $user->setAuthSecret($I->faker()->word());

        $request = $I->getRequest();
        $service = Stub::make(TwoFactorService::class, [
            'validateCode' => true,
        ]);

        $dto = $service->checkCode($user, $code, $request);

        $I->assertEquals(true, $dto->isCorrect);
        $I->assertEquals(null, $dto->error);
    }

    public function negativeCheckCodeEmptyCode(TwoFactorServiceStep $I): void
    {
        $user = $I->getUser();
        $user->setAuthSecret($I->faker()->word());

        $request = $I->getRequest();
        $service = Stub::make(TwoFactorService::class, [
            'validateCode' => true,
        ]);

        $dto = $service->checkCode($user, null, $request);

        $I->assertEquals(false, $dto->isCorrect);
        $I->assertEquals('Empty code', $dto->error);
    }

    public function negativeCheckCodeNotCorrectCode(TwoFactorServiceStep $I): void
    {
        $user = $I->getUser();
        $code = $I->faker()->word();
        $user->setAuthSecret($I->faker()->word());

        $request = $I->getRequest();
        $service = Stub::make(TwoFactorService::class, [
            'validateCode' => false,
        ]);

        $dto = $service->checkCode($user, $code, $request);

        $I->assertEquals(false, $dto->isCorrect);
        $I->assertEquals('Not correct code', $dto->error);
    }
}