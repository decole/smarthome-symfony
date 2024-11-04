<?php

declare(strict_types=1);

namespace App\Application\Http\Web\VisualNotification\Dto;

final class VisualNotificationHistoryInputDto
{
    public int $page = 1;
    public int $limit = 12;
}