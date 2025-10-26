<?php

declare(strict_types=1);

namespace App\Domain\Common\Embedded;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embeddable;

#[Embeddable]
final class StatusMessage
{
    public function __construct(
        #[ORM\Column(type: Types::STRING, nullable: true)]
        private readonly ?string $message_info = null,
        #[ORM\Column(type: Types::STRING, nullable: true)]
        private readonly ?string $message_ok = null,
        #[ORM\Column(type: Types::STRING, nullable: true)]
        private readonly ?string $message_warn = null,
    ) {}

    public function getMessageInfo(): ?string
    {
        return $this->message_info;
    }

    public function getMessageOk(): ?string
    {
        return $this->message_ok;
    }

    public function getMessageWarn(): ?string
    {
        return $this->message_warn;
    }
}
