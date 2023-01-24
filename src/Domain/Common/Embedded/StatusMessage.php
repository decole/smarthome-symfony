<?php

namespace App\Domain\Common\Embedded;


final class StatusMessage
{
    public function __construct(
        private readonly ?string $message_info = null,
        private readonly ?string $message_ok = null,
        private readonly ?string $message_warn = null
    ) {
    }

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