<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230304222252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add columns by restorePassword';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "user" ADD restore_token VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD restore_token_created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN "user".restore_token_created_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" DROP restore_token');
        $this->addSql('ALTER TABLE "user" DROP restore_token_created_at');
    }
}