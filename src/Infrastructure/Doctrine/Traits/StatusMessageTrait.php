<?php

namespace App\Infrastructure\Doctrine\Traits;

use App\Application\Service\Validation\ValidationDtoInterface;
use App\Infrastructure\Doctrine\Interfaces\EntityInterface;

trait StatusMessageTrait
{
    private function setStatusMessage(ValidationDtoInterface $dto, EntityInterface $entity): void
    {
        $dto->message_info = $entity->getStatusMessage()->getMessageInfo();
        $dto->message_ok = $entity->getStatusMessage()->getMessageOk();
        $dto->message_warn = $entity->getStatusMessage()->getMessageWarn();
    }
}