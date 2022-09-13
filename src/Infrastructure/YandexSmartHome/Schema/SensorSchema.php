<?php

namespace App\Infrastructure\YandexSmartHome\Schema;

class SensorSchema implements SchemaInterface
{
    public function __construct(int $id, ?string $state)
    {
        $this->id = $id;
        $this->state = $state;
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