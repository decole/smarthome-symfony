<?php

declare(strict_types=1);

namespace App\Domain\Notification\Criteria;

final class FireSecureCriteria extends AbstractCriteria
{
    public function prepareAlertMessage(): string
    {
        $deviceAlertMessage = $this->dto->device->getStatusMessage()?->getMessageWarning() ?? null;

        if (empty($deviceAlertMessage)) {
            $deviceAlertMessage = 'Внимание! Пожар!';
        }

        return $deviceAlertMessage;
    }
}
