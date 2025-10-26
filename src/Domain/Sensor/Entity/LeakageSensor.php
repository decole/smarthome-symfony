<?php

declare(strict_types=1);

namespace App\Domain\Sensor\Entity;

use App\Domain\Common\Embedded\StatusMessage;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'sensor_leakage')]
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
        #[ORM\Column(type: Types::STRING, nullable: true)]
        private ?string $payloadDry = null,
        #[ORM\Column(type: Types::STRING, nullable: true)]
        private ?string $payloadWet = null,
    ) {
        parent::__construct(
            $this->name,
            $this->topic,
            $this->payload,
            $this->statusMessage,
            $this->status,
            $this->notify,
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
