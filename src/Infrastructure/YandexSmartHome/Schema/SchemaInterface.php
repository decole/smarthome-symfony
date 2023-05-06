<?php

namespace App\Infrastructure\YandexSmartHome\Schema;

interface SchemaInterface
{
    public function getSchema(): array;

    public function setState(string $state): void;
}