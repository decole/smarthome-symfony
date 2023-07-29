<?php

declare(strict_types=1);

namespace App\Infrastructure\YandexSmartHome\Schema;

final class RelaySchema implements SchemaInterface
{
    private ?string $state = null;

    public function __construct(
        private readonly string $id,
    ) {
    }

    public function getSchema(): array
    {
        return [
            "id" => $this->id,
            "capabilities" => [
                [
                    "type" => "devices.capabilities.on_off",
                    "retrievable" => true,
                    "state" => [
                        'instance' => 'on',
                        "value" => $this->state,
                        "action_result" => [
                            "status" => "DONE"
                        ],
                    ],
                ]
            ],
        ];
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }
}