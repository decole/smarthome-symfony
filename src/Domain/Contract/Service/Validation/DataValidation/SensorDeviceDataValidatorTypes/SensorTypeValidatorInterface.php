<?php

declare(strict_types=1);

namespace App\Domain\Contract\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes;

interface SensorTypeValidatorInterface
{
    /**
     * Проверка на адекватность пришедших значений.
     *
     * @return bool
     */
    public function validate(): bool;

    /**
     * Сработка по условию типа датчика. Выход за приделы нормы
     *
     * @return bool
     */
    public function isAlert(): bool;
}