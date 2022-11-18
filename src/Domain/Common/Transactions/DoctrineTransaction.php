<?php

namespace App\Domain\Common\Transactions;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Throwable;

class DoctrineTransaction implements TransactionInterface
{
    private Connection $connection;

    public function __construct(private EntityManager $manager, private string $env)
    {
        $this->connection = $manager->getConnection();
    }

    /** @inheritdoc */
    public function flush($entity = null): void
    {
        $this->manager->flush($entity);
    }

    /** @inheritdoc */
    public function transactional(callable $scope, ?callable $failOver = null)
    {
        $this->connection->beginTransaction();

        try {
            $returned = $scope();

            $this->manager->flush();
            $this->connection->commit();

            return $returned;
        } catch (Throwable $e) {
            if ($failOver !== null) {
                return $failOver($this->connection);
            }

            // Не закрываем entity manager в тестовом окружении
            if ($this->env !== 'test') {
                $this->manager->close();
            }
            $this->connection->rollBack();

            throw $e;
        }
    }

    public function commit(...$entities): void
    {
        $this->connection->beginTransaction();

        try {
            foreach ($entities as $entity) {
                $this->manager->persist($entity);
            }

            $this->manager->flush($entities);
            $this->connection->commit();
        } catch (Throwable $exception) {
            // Не закрываем entity manager в тестовом окружении
            if ($this->env !== 'test') {
                $this->manager->close();
            }
            $this->connection->rollBack();

            throw $exception;
        }
    }
}