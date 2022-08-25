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

        private ?string $payload_min = null,
        private ?string $payload_max = null
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
        return $this->payload_min;
    }

    final public function setPayloadMin(?string $payload): void
    {
        $this->payload_min = $payload;
    }

    final public function getPayloadMax(): ?string
    {
        return $this->payload_max;
    }

    final public function setPayloadMax(?string $payload): void
    {
        $this->payload_max = $payload;
    }
}