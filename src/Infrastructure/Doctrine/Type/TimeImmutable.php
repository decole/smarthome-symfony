<?php


namespace App\Infrastructure\Doctrine\Type;


use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\TimeImmutableType;

class TimeImmutable extends TimeImmutableType
{
    public function getName(): string
    {
        return 'time_immutable';
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        /** @var  $value \DateTimeInterface */
        if ($value instanceof \DatetimeImmutable) {
            return $this->convertDateTimeToUTC($value)->format($platform->getTimeFormatString());
        }

        throw ConversionException::conversionFailedInvalidType(
            $value,
            $this->getName(),
            ['null', __CLASS__]
        );
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
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