<?php

declare(strict_types=1);

namespace App\Domain\Contract\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes;

interface SensorTypeValidatorInterface
{
    /**
     * Проверка на адекватность пришедших значений.
     * True - все в порядке.
     */
    public function validate(): bool;

    /**
     * Несовпадение хранимого статуса. Внезапное изменение статуса.
     */
    public function validateStatus(): bool;
}
