<?php


namespace App\Infrastructure\Doctrine\Interfaces;


use App\Domain\Doctrine\Relay\Entity\Relay;

interface RelayRepositoryInterface
{
    public function save(EntityInterface $sensor): EntityInterface;

    public function findAll(): array;

    public function findById(string $id): ?Relay;

    public function findByName(string $value): ?Relay;

    public function findByTopic(string $value): ?Relay;
}