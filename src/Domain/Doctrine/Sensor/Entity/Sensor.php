<?php


namespace App\Domain\Doctrine\Sensor\Entity;


use App\Domain\Doctrine\Common\Traits\CreatedAt;
use App\Domain\Doctrine\Common\Traits\Entity;
use App\Domain\Doctrine\Common\Traits\UpdatedAt;

class Sensor
{
    public const STATUS_ACTIVE = 1;
    public const STATUS_DESACTIVE = 0;

    use Entity, CreatedAt, UpdatedAt;

    public function __construct(
        private string $name,
        private string $topic,
        private string $payload,
        private string $message_info,
        private string $message_ok,
        private string $message_warn,
        private int $type,
        private int $status,
        private bool $notify,
        private int $payload_min,
        private int $payload_max,
    ) {
        $this->identify();
        $this->onCreated();
    }
}