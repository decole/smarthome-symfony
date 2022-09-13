<?php

namespace App\Infrastructure\YandexSmartHome\Dto;

use Modules\AliceSmartHome\Services\Devices\Schemas\SchemaInterface;

class DeviceDto
{
    public string $id;

    public string $type;

    public mixed $state;

    public SchemaInterface $schema;
}