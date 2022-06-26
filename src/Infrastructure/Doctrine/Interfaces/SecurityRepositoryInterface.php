<?php


namespace App\Infrastructure\Doctrine\Interfaces;


use App\Domain\Doctrine\Security\Entity\Security;

interface SecurityRepositoryInterface
{
    public function save(EntityInterface $sensor): EntityInterface;

    public function findAll(): array;

    public function findById(string $id): ?Security;

    public function findByName(string $value): ?Security;

    public function findByTopic(string $value): ?Security;
}