<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeImmutableType;

final class DatetimeImmutable extends DateTimeImmutableType
{
    public static function getTypeName(): string
    {
        return 'datetime_immutable';
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof \DatetimeImmutable) {
            return $this->convertDateTimeToUTC($value)->format($platform->getDateTimeTzFormatString());
        }

        throw ConversionException::conversionFailedInvalidType(
            $value,
            $this->getName(),
            ['null', __CLASS__]
        );
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?\DateTimeImmutable
    {
        if ($value === null || $value instanceof \DateTimeImmutable) {
            return $value;
        }

        $dateTime = \DateTimeImmutable::createFromFormat(
            $platform->getDateTimeFormatString(),
            $value,
            new \DateTimeZone('UTC')
        );

        if (!$dateTime) {
            $dateTime = date_create_immutable($value);
        }

        if (!$dateTime) {
            throw ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                $platform->getDateTimeFormatString()
            );
        }

        return $dateTime;
    }

    /**
     * Конвертирует дату и время в utc
     *
     * @param \DateTimeImmutable $dateTime
     * @return \DateTimeImmutable
     * @throws \Exception
     */
    private function convertDateTimeToUTC(\DateTimeImmutable $dateTime): \DateTimeImmutable
    {
        $convertDateTime = new \DateTime($dateTime->format(\DateTime::ATOM));
        $convertDateTime->setTimezone(new \DateTimeZone('UTC'));
        return \DateTimeImmutable::createFromMutable($convertDateTime);
    }
}