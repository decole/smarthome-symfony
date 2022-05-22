<?php


namespace App\Domain\Doctrine\Identity\Repository;


use App\Domain\Doctrine\Identity\Entity\User;

interface UserRepositoryInterface
{
    public function save(User $user): void;
}