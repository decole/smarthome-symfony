<?php

declare(strict_types=1);

namespace App\Domain\Notification\Criteria;

final class SensorCriteria extends AbstractCriteria
{
    public function prepareAlertMessage(): string
    {
        $text = $this->dto->device->getStatusMessage()?->getMessageWarning() ?? null;

        if (empty($text)) {
            $name = $this->dto->device->getName() ?? $this->dto->device->getTopic();

            $text = \sprintf('Внимание! Сенсор %s имеет неопознанное состояние [{value}] !', $name);
        }

        return $text;
    }
}
