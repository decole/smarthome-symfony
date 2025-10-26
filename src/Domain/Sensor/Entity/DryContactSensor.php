<?php

declare(strict_types=1);

namespace App\Domain\Sensor\Entity;

use App\Domain\Common\Embedded\StatusMessage;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'sensor_dry_contact')]
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
        #[ORM\Column(type: Types::STRING, nullable: true)]
        private ?string $payloadHigh = null,
        #[ORM\Column(type: Types::STRING, nullable: true)]
        private ?string $payloadLow = null,
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
