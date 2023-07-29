<?php

declare(strict_types=1);

namespace App\Infrastructure\AliceSkill\Dto;

class AliceDialogDto
{
    public string $text;

    public bool $isFinishSession;

    public string $sessionId;

    public string $messageId;

    public string $userId;
}