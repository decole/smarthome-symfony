<?php

namespace App\Infrastructure\YandexSmartHome\Dto;

use App\Infrastructure\YandexSmartHome\Schema\SchemaInterface;

class DeviceDto
{
    public string $id;

    public string $type;

    public mixed $state;

    public SchemaInterface $schema;
}