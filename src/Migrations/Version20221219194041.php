<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221219194041 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE page ADD icon VARCHAR(255) NOT NULL DEFAULT \'fas fa-home\'');
        $this->addSql('ALTER TABLE page ADD alias VARCHAR(255) NOT NULL DEFAULT \'test\'');
        $this->addSql('ALTER TABLE page ADD group_id INT NOT NULL DEFAULT 0');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE page DROP icon');
        $this->addSql('ALTER TABLE page DROP alias');
        $this->addSql('ALTER TABLE page DROP group_id');
    }
}
