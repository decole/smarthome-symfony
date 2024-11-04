<?php

declare(strict_types=1);

namespace App\Domain\Contract\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes;

interface SensorTypeValidatorInterface
{
    /**
     * Проверка на адекватность пришедших значений.
     */
    public function validate(): bool;

    /**
     * Сработка по условию типа датчика. Выход за приделы нормы
     */
    public function isAlert(): bool;
}