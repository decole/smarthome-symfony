<?php

namespace App\Domain\Doctrine\Sensor\Entity;

use App\Domain\Doctrine\Common\Embedded\StatusMessage;

class SensorDryContact extends Sensor
{
    public const TYPE = 'dryContact';

    public function __construct(
        private string $name,
        private string $topic,
        private ?string $payload,
        private StatusMessage $statusMessage,
        private int $status,
        private bool $notify,

        private ?string $payload_high = null,
        private ?string $payload_low = null
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

    final public function getPayloadHigh(): ?string
    {
        return $this->payload_high;
    }

    final public function setPayloadHigh(?string $payload): void
    {
        $this->payload_high = $payload;
    }

    final public function getPayloadLow(): ?string
    {
        return $this->payload_low;
    }

    final public function setPayloadLow(?string $payload): void
    {
        $this->payload_low = $payload;
    }
}