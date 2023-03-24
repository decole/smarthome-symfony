<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230324000611 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add google auth by user';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "user" ADD google_authenticator_secret VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" DROP google_authenticator_secret');
    }
}
