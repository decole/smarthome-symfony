<?php


namespace App\Infrastructure\Doctrine\Traits;


use App\Application\Service\Validation\ValidationDtoInterface;
use App\Domain\Contract\Repository\EntityInterface;

trait CommonCrudFieldTraits
{
    private function setEntityToDtoCommonParams(
        ValidationDtoInterface $dto,
        EntityInterface $entity,
        bool $setType = true
    ): void {
        if ($setType) {
            $dto->type = $entity->getType();
        }
        $dto->name = $entity->getName();
        $dto->topic = $entity->getTopic();
        $dto->payload = $entity->getPayload();

        $dto->notify = $entity->isNotify() ? 'on' : null;
    }

    private function setDtoToEntityCommonParams(EntityInterface $entity, ValidationDtoInterface $dto): void
    {
        $entity->setName($dto->name);
        $entity->setTopic($dto->topic);
        $entity->setPayload($dto->payload);
    }
}
