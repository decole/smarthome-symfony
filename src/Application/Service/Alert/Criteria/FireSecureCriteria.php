<?php

namespace App\Application\Service\Alert\Criteria;

use App\Domain\FireSecurity\Entity\FireSecurity;

final class FireSecureCriteria extends AbstractCriteria
{
    /**
     * В любом случае при сработке пожарного датчика будет оповещение.
     *
     * @return void
     */
    public function notify(): void
    {
        $this->sendByVisualNotify();
        $this->sendByMessengers();
    }

    public function prepareAlertMessage(): string
    {
        /** @var FireSecurity $device */
        $deviceAlertMessage = $this->device?->getStatusMessage()?->getMessageWarn();

        return $deviceAlertMessage ?? 'Внимание! Пожар!';
    }
}