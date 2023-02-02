<?php

namespace App\Tests\_support\Step\Api;

use App\Application\Http\Web\Security\Dto\CrudSecurityDto;
use App\Domain\Security\Entity\Security;
use App\Domain\Security\Enum\SecurityStateEnum;
use App\Domain\Security\Service\SecurityCrudService;
use App\Tests\ApiTester;

class SecureDeviceDataStep extends ApiTester
{
    public function secureDeviceState(?string $topic): void
    {
        $this->sendGet('/secure/state', [
            'topic' => $topic,
        ]);
    }

    public function secureSetTrigger(string $topic, ?bool $state): void
    {
        if ($state === null) {
            $this->sendPost('/secure/trigger', [
                'topic' => $topic,
                'trigger' => null,
            ]);

            return;
        }

        $this->sendPost('/secure/trigger', [
            'topic' => $topic,
            'trigger' => $state === true ? 'true' : 'false',
        ]);
    }

    public function createSecureDevice(
        bool $isTriggered,
        bool $isGuarded,
        bool $isActive = true,
        bool $isNotify = true
    ): Security {
        $dto = new CrudSecurityDto();

        $dto->type = 'mqtt_security_device';
        $dto->name = $this->faker()->word();
        $dto->topic = $this->faker()->word();
        $dto->payload = $isTriggered === true ? 1 : 0;
        $dto->detectPayload = 1;
        $dto->holdPayload = 0;
        $dto->lastCommand = $isGuarded === true ? SecurityStateEnum::GUARD_STATE->value :
            SecurityStateEnum::HOLD_STATE->value;
        $dto->message_info = $this->faker()->word();
        $dto->message_ok = $this->faker()->word();
        $dto->message_warn = $this->faker()->word();
        $dto->status = $isActive === true ? 'on' : 'off';
        $dto->notify = $isNotify === true ? 'on' : 'off';

        return $this->grabService(SecurityCrudService::class)->create($dto);
    }
}