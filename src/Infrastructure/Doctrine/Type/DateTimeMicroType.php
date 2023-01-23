<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Type;

use DateTime;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

final class DateTimeMicroType extends Type
{
    public const TYPE_NAME = 'datetime_microseconds';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'TIMESTAMP(6) WITHOUT TIME ZONE';
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return bool|DateTime|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): bool|null|DateTime
    {
        if ($value === null) {
            return null;
        }

        // Если была создана запись с датой Y-m-d h:i:s.000000,
        // то из PDO придет значение без миллисекунд
        if (!str_contains($value, '.')) {
            $value .= '.000000';
        }

        return DateTime::createFromFormat($this->formatString(), $value);
    }

    /**
     * @return string
     */
    protected function formatString(): string
    {
        return 'Y-m-d H:i:s.u';
    }


    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof DateTime) {
            return $value->format($this->formatString());
        }

        throw ConversionException::conversionFailedInvalidType(
            $value,
            $this->getName(),
            ['null', 'DateTime']
        );
    }

    public function getName(): string
    {
        return self::TYPE_NAME;
    }
}