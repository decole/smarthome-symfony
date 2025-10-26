<?php

declare(strict_types=1);

namespace App\Domain\Sensor\Entity;

use App\Domain\Common\Embedded\StatusMessage;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'sensor_humidity')]
class HumiditySensor extends Sensor
{
    public const TYPE = 'humidity';

    public function __construct(
        private string $name,
        private string $topic,
        private ?string $payload,
        private StatusMessage $statusMessage,
        private int $status,
        private bool $notify,
        #[ORM\Column(type: Types::STRING, nullable: true)]
        private ?string $payloadMin = null,
        #[ORM\Column(type: Types::STRING, nullable: true)]
        private ?string $payloadMax = null,
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
