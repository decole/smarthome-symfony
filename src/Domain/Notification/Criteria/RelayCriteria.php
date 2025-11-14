<?php

declare(strict_types=1);

namespace App\Domain\Notification\Criteria;

final class RelayCriteria extends AbstractCriteria
{
    public function prepareAlertMessage(): string
    {
        $text = $this->dto->device->getStatusMessage()?->getMessageWarning() ?? null;

        if (empty($text)) {
            $name = $this->dto->device->getName() ?? $this->dto->devicePayload->getTopic();

            $text = \sprintf('Внимание! Реле %s имеет неопознанное состояние [{value}] !', $name);
        }

        return $text;
    }
}
