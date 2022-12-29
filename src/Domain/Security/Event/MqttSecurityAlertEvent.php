<?php

namespace App\Domain\Security\Event;

use App\Domain\Payload\Entity\DevicePayload;
use App\Domain\Security\Entity\Security;
use Symfony\Contracts\EventDispatcher\Event;

class MqttSecurityAlertEvent extends Event
{
    public const NAME = 'security.alert';

    public function __construct(private Security $device, private DevicePayload $payload)
    {
    }

    public function getDevice(): Security
    {
        return $this->device;
    }

    public function getPayload(): DevicePayload
    {
        return $this->payload;
    }
}