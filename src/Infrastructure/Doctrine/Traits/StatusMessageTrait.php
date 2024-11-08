<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Traits;

use App\Domain\Contract\CrudValidation\ValidationDtoInterface;
use App\Domain\Contract\Repository\EntityInterface;

trait StatusMessageTrait
{
    private function setStatusMessage(ValidationDtoInterface $dto, EntityInterface $entity): void
    {
        $dto->message_info = $entity->getStatusMessage()->getMessageInfo();
        $dto->message_ok = $entity->getStatusMessage()->getMessageOk();
        $dto->message_warn = $entity->getStatusMessage()->getMessageWarn();
    }
}