<?php


namespace App\Domain\Doctrine\Identity\Entity;


final class Contact
{
    private string $email;

    public function __construct(string $email, private ?string $telegram = null, private ?int $telegramId = null)
    {
        $this->email = mb_strtolower($email);
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): Contact
    {
        $this->email = $email;

        return $this;
    }

    public function getTelegram(): ?string
    {
        return $this->telegram;
    }

    public function setTelegram(string $telegram): Contact
    {
        $this->telegram = $telegram;
        return $this;
    }

    public function getTelegramId(): ?int
    {
        return $this->telegramId;
    }

    public function setTelegramId(?int $telegramId): self
    {
        $this->telegramId = $telegramId;
        return $this;
    }
}