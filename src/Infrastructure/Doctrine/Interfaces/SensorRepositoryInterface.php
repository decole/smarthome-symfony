<?php


namespace App\Infrastructure\Doctrine\Interfaces;


use App\Domain\Doctrine\Sensor\Entity\Sensor;

interface SensorRepositoryInterface
{
    public function save(EntityInterface $sensor): EntityInterface;

    public function findAll(): array;

    public function findById(string $id): ?Sensor;

    public function findByName(string $value): ?Sensor;

    public function findByTopic(string $value): ?Sensor;
}