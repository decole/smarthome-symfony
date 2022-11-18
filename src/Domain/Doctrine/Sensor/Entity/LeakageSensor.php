<?php

namespace App\Domain\Doctrine\Sensor\Entity;

use App\Domain\Doctrine\Common\Embedded\StatusMessage;

class LeakageSensor extends Sensor
{
    public const TYPE = 'leakage';

    public function __construct(
        private string $name,
        private string $topic,
        private ?string $payload,
        private StatusMessage $statusMessage,
        private int $status,
        private bool $notify,

        private ?string $payloadDry = null,
        private ?string $payloadWet = null
    ) {
        parent::__construct(
            $this->name,
            $this->topic,
            $this->payload,
            $this->statusMessage,
            $this->status,
            $this->notify
        );
    }

    final public function getPayloadDry(): ?string
    {
        return $this->payloadDry;
    }

    final public function setPayloadDry(?string $payload): void
    {
        $this->payloadDry = $payload;
    }

    final public function getPayloadWet(): ?string
    {
        return $this->payloadWet;
    }

    final public function setPayloadWet(?string $payload): void
    {
        $this->payloadWet = $payload;
    }
}