<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Type;

use DateTimeImmutable;
use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

class DateTimeImmutableMicroType extends Type
{
    final public const TYPE_NAME = 'datetime_immutable_microseconds';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'TIMESTAMP(6) WITHOUT TIME ZONE';
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return bool|DateTimeImmutable|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): bool|null|DateTimeImmutable
    {
        if ($value === null) {
            return null;
        }

        // Если была создана запись с датой Y-m-d h:i:s.000000,
        // то из PDO придет значение без миллисекунд
        if (!str_contains($value, '.')) {
            $value .= '.000000';
        }

        return DateTimeImmutable::createFromFormat($this->formatString(), $value, new DateTimeZone('UTC'));
    }

    /**
     * @return string
     */
    protected function formatString(): string
    {
        return 'Y-m-d H:i:s.u';
    }


    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof DateTimeImmutable) {
            return $value->format($this->formatString());
        }

        throw ConversionException::conversionFailedInvalidType(
            $value,
            $this->getName(),
            ['null', 'DateTimeImmutable']
        );
    }

    public function getName(): string
    {
        return self::TYPE_NAME;
    }
}