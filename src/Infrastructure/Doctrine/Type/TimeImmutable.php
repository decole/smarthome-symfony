<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Type;

use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;

final class TimeImmutable extends \Doctrine\DBAL\Types\DateTimeImmutableType
{
    public function getName(): string
    {
        return 'time_immutable';
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        /** @psalm-suppress InvalidClass */
        if ($value instanceof \DatetimeImmutable) {
            return $this->convertDateTimeToUTC($value)->format($platform->getTimeFormatString());
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
            '!' . $platform->getTimeFormatString(),
            $value,
            new DateTimeZone('UTC')
        );

        if (!$dateTime) {
            throw ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                $platform->getTimeFormatString()
            );
        }

        return $dateTime;
    }

    /**
     * Конвертирует дату и время в utc
     *
     * @throws \Exception
     */
    private function convertDateTimeToUTC(\DateTimeImmutable $dateTime): \DateTimeImmutable
    {
        $convertDateTime = new \DateTime($dateTime->format(\DateTime::ATOM));
        $convertDateTime->setTimezone(new \DateTimeZone('UTC'));
        return \DateTimeImmutable::createFromMutable($convertDateTime);
    }
}