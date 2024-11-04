<?php

declare(strict_types=1);

namespace App\Infrastructure\AliceSkill\Dto;

class AliceSkillResponseDto
{
    public function __construct(private AliceDialogDto $dto)
    {
    }

    /**
     * https://yandex.ru/dev/dialogs/alice/doc/response.html
     */
    public function getResult(): array
    {
        return [
            'response' =>
                [
                    'text'        => $this->dto->text,
                    'tts'         => $this->dto->text,
                    'end_session' => $this->dto->isFinishSession,
                ],
            'session_state' => $this->dto->messageId,
            'session' =>
                [
                    'session_id'  => $this->dto->sessionId,
                    'message_id'  => $this->dto->messageId,
                    'user_id'     => $this->dto->userId,
                ],
            'version' => '1.0',
        ];
    }
}