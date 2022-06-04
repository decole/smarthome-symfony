<?php


namespace App\Domain\Doctrine\Identity\Repository;


use App\Domain\Doctrine\Identity\Entity\User;

interface UserRepositoryInterface
{
    public function add(User $entity, bool $flush = false): void;

    public function remove(User $entity, bool $flush = false): void;

    public function findOneByLogin(string $login): ?User;
}