<?php

namespace App\Infrastructure\AliceSkill\Dto;

class AliceSkillRequestDto
{
    private string $messageId;

    private string $sessionId;

    private string $skillId;

    private string $userId;

    private string $applicationId;

    private string $clientId;

    private bool $isNewSession;

    private ?string $command;

    private ?string $originalUtterance;

    private array $nluTokenList;

    private string $type;

    private string $version;

    public function __construct(?array $request)
    {
        $this->messageId = $request['session']['message_id'] ?? '';
        $this->sessionId = $request['session']['session_id'] ?? '';
        $this->skillId = $request['session']['skill_id'] ?? '';
        $this->userId = $request['session']['user']['user_id'] ?? '';
        $this->applicationId = $request['session']['application']['application_id'] ?? '';
        $this->clientId = $request['session']['user_id'] ?? '';
        $this->isNewSession = $request['session']['new'] ?? true;

        $this->command = mb_strtolower($request['request']['command'] ?? null);
        $this->originalUtterance = $request['request']['original_utterance'] ?? null;
        $this->nluTokenList = $request['request']['nlu']['tokens'] ?? [];
        $this->type = $request['request']['type'] ?? 'SimpleUtterance';

        $this->version = $request['version'] ?? '1.0';
    }

    public function getMessageId(): string
    {
        return $this->messageId;
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    public function getSkillId(): string
    {
        return $this->skillId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getApplicationId(): string
    {
        return $this->applicationId;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function isNewSession(): bool
    {
        return $this->isNewSession;
    }

    public function getCommand(): ?string
    {
        return $this->command;
    }

    public function getOriginalUtterance(): ?string
    {
        return $this->originalUtterance;
    }

    public function getNluTokenList(): array
    {
        return $this->nluTokenList;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getVersion(): string
    {
        return $this->version;
    }
}