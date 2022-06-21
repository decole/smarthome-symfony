<?php


namespace App\Domain\Doctrine\Sensor\Entity;


use App\Domain\Doctrine\DeviceCommon\Entity\StatusMessage;

class SensorLeakage extends Sensor
{
    public const TYPE = 'leakage';

    public function __construct(
        private string $name,
        private string $topic,
        private ?string $payload,
        private StatusMessage $statusMessage,
        private int $status,
        private bool $notify,

        private ?string $payload_dry = null,
        private ?string $payload_wet = null,
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
        return $this->payload_dry;
    }

    final public function setPayloadDry(?string $payload): void
    {
        $this->payload_dry = $payload;
    }

    final public function getPayloadWet(): ?string
    {
        return $this->payload_wet;
    }

    final public function setPayloadWet(?string $payload): void
    {
        $this->payload_wet = $payload;
    }
}