<?php


namespace App\Domain\Doctrine\Common\Embedded;


class StatusMessage
{
    public function __construct(
        private ?string $message_info = null,
        private ?string $message_ok = null,
        private ?string $message_warn = null
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