<?php

declare(strict_types=1);

namespace App\Infrastructure\AliceSkill\Dialog;

use App\Infrastructure\AliceSkill\Dto\AliceDialogDto;
use App\Infrastructure\AliceSkill\Dto\AliceSkillRequestDto;
use App\Infrastructure\AliceSkill\Dto\AliceSkillResponseDto;

final class PingDialog extends AbstractDialog implements AliceDialogInterface
{
    public static function getCommandVerbList(): array
    {
        return [
            'ping',
            'пинг',
        ];
    }

    public static function getInstance(AliceSkillRequestDto $dto): self
    {
        return new self($dto);
    }

    public function getAnswer(): AliceSkillResponseDto
    {
        $dto = new AliceDialogDto();
        $dto->sessionId = $this->requestDto->getSessionId();
        $dto->messageId = $this->requestDto->getMessageId();
        $dto->userId = $this->requestDto->getClientId();
        $dto->text = 'понг';
        $dto->isFinishSession = true;

        return new AliceSkillResponseDto($dto);
    }
}