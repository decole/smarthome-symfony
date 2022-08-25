<?php

namespace App\Domain\Payload;

final class DevicePayload
{
    public function __construct(private string $topic, private string $payload)
    {
    }

    public function getTopic(): string
    {
        return $this->topic;
    }

    public function getPayload(): string
    {
        return $this->payload;
    }
}
