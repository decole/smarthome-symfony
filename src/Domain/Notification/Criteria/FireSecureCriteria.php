<?php

declare(strict_types=1);

namespace App\Domain\Notification\Criteria;

use App\Domain\FireSecurity\Entity\FireSecurity;

final class FireSecureCriteria extends AbstractCriteria
{
    /**
     * В любом случае при сработке пожарного датчика будет оповещение.
     */
    public function notify(): void
    {
        $this->sendByVisualNotify();
        $this->sendByMessengers();
    }

    public function prepareAlertMessage(): string
    {
        $deviceAlertMessage = $this->device?->getStatusMessage()?->getMessageWarn();

        return $deviceAlertMessage ?? 'Внимание! Пожар!';
    }
}