<?php

namespace App\Domain\Doctrine\Sensor\Entity;

use App\Domain\Doctrine\Common\Embedded\StatusMessage;

class SensorPressure extends Sensor
{
    public const TYPE = 'pressure';

    public function __construct(
        private string $name,
        private string $topic,
        private ?string $payload,
        private StatusMessage $statusMessage,
        private int $status,
        private bool $notify,

        private ?string $payloadMin = null,
        private ?string $payloadMax = null
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

    final public function getPayloadMin(): ?string
    {
        return $this->payloadMin;
    }

    final public function setPayloadMin(?string $payload): void
    {
        $this->payloadMin = $payload;
    }

    final public function getPayloadMax(): ?string
    {
        return $this->payloadMax;
    }

    final public function setPayloadMax(?string $payload): void
    {
        $this->payloadMax = $payload;
    }
}