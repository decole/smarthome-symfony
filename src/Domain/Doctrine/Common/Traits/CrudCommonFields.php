<?php

namespace App\Domain\Doctrine\Common\Traits;


trait CrudCommonFields
{
    final public function getName(): string
    {
        return $this->name;
    }

    final public function setName(string $name): void
    {
        $this->name = $name;
    }

    final public function getTopic(): string
    {
        return $this->topic;
    }

    final public function setTopic(string $topic): void
    {
        $this->topic = $topic;
    }

    final public function getPayload(): ?string
    {
        return $this->payload;
    }

    final public function setPayload(?string $payload): void
    {
        $this->payload = $payload;
    }
}