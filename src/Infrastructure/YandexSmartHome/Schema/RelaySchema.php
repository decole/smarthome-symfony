<?php

namespace App\Infrastructure\YandexSmartHome\Schema;

class RelaySchema implements SchemaInterface
{
    public function __construct(private string $id, private mixed $state)
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
}