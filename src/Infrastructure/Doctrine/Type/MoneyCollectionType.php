<?php

namespace App\Infrastructure\Doctrine\Type;

use App\Domain\Doctrine\Common\Enum\CurrencyEnum;
use App\Domain\Doctrine\Common\Money;
use App\Domain\Doctrine\Common\ValueObject\MoneyCollection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

final class MoneyCollectionType extends Type
{
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value instanceof MoneyCollection) {
            $parsed = [];

            foreach ($value->getCollection() as $money) {
                $parsed[$money->getCurrency()] = $money->getValue();
            }

            return json_encode($parsed, JSON_THROW_ON_ERROR);
        }

        throw ConversionException::conversionFailedFormat(
            $value,
            $this->getName(),
            'MoneyCollection'
        );
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): MoneyCollection
    {
        /** @var array<string, float> $value */
        $value = json_decode($value ?? '[]', true, 512, JSON_THROW_ON_ERROR);

        $collection = new MoneyCollection();

        foreach (CurrencyEnum::all() as $currency) {
            $collection->add(new Money($currency, $value[$currency] ?? 0));
        }

        return $collection;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getFloatDeclarationSQL($column);
    }

    public function getName(): string
    {
        return 'money_collection';
    }
}