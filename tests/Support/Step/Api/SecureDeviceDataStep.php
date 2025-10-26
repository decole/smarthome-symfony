<?php

declare(strict_types=1);

namespace App\Tests\Support\Step\Api;

use App\Application\Http\Web\Security\Dto\CrudSecurityDto;
use App\Domain\Security\Entity\Security;
use App\Domain\Security\Enum\SecurityStateEnum;
use App\Domain\Security\Service\SecurityCrudService;
use App\Tests\Support\ApiTester;

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
        if (null === $state) {
            $this->sendPost('/secure/trigger', [
                'topic' => $topic,
                'trigger' => null,
            ]);

            return;
        }

        $this->sendPost('/secure/trigger', [
            'topic' => $topic,
            'trigger' => true === $state ? 'true' : 'false',
        ]);
    }

    public function createSecureDevice(
        bool $isTriggered,
        bool $isGuarded,
        bool $isActive = true,
        bool $isNotify = true,
    ): Security {
        $dto = new CrudSecurityDto();

        $dto->type = 'mqtt_security_device';
        $dto->name = $this->faker()->word();
        $dto->topic = $this->faker()->word();
        $dto->payload = true === $isTriggered ? 1 : 0;
        $dto->detectPayload = 1;
        $dto->holdPayload = 0;
        $dto->lastCommand = true === $isGuarded ? SecurityStateEnum::GUARD_STATE->value :
            SecurityStateEnum::HOLD_STATE->value;
        $dto->message_info = $this->faker()->word();
        $dto->message_ok = $this->faker()->word();
        $dto->message_warn = $this->faker()->word();
        $dto->status = true === $isActive ? 'on' : 'off';
        $dto->notify = true === $isNotify ? 'on' : 'off';

        return $this->grabService(SecurityCrudService::class)->create($dto);
    }
}
