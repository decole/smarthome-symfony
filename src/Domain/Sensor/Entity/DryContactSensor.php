<?php

declare(strict_types=1);

namespace App\Domain\Sensor\Entity;

use App\Domain\Common\Embedded\StatusMessage;

class DryContactSensor extends Sensor
{
    public const TYPE = 'dryContact';

    public function __construct(
        private string $name,
        private string $topic,
        private ?string $payload,
        private StatusMessage $statusMessage,
        private int $status,
        private bool $notify,

        private ?string $payloadHigh = null,
        private ?string $payloadLow = null
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
        return $this->payloadHigh;
    }

    final public function setPayloadHigh(?string $payload): void
    {
        $this->payloadHigh = $payload;
    }

    final public function getPayloadLow(): ?string
    {
        return $this->payloadLow;
    }

    final public function setPayloadLow(?string $payload): void
    {
        $this->payloadLow = $payload;
    }
}