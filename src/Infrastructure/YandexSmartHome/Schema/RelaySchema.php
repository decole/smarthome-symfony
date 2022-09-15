<?php

namespace App\Infrastructure\YandexSmartHome\Schema;

class RelaySchema implements SchemaInterface
{
    private ?string $state = null;

    public function __construct(private string $id)
    {
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