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
        #[ORM\Column(type: Types::STRING, nullable: true, name: 'message_info')]
        private readonly ?string $messageInfo = null,
        #[ORM\Column(type: Types::STRING, nullable: true, name: 'message_ok')]
        private readonly ?string $messageOk = null,
        #[ORM\Column(type: Types::STRING, nullable: true, name: 'message_warn')]
        private readonly ?string $messageWarning = null,
    ) {}

    public function getMessageInfo(): ?string
    {
        return $this->messageInfo;
    }

    public function getMessageOk(): ?string
    {
        return $this->messageOk;
    }

    public function getMessageWarning(): ?string
    {
        return $this->messageWarning;
    }
}
