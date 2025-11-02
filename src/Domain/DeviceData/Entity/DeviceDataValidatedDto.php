<?php

declare(strict_types=1);

namespace App\Domain\DeviceData\Entity;

use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\Payload\Entity\DevicePayload;

final class DeviceDataValidatedDto
{
    public function __construct(
        public readonly DevicePayload $devicePayload,
        public readonly EntityInterface $device,
        public bool $hasCheckStatusWarning,
        public bool $hasAlertingNotify,
    ) {}
}
