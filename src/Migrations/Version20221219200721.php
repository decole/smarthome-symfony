<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221219200721 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE page ALTER icon DROP DEFAULT');
        $this->addSql('ALTER TABLE page ALTER alias DROP DEFAULT');
        $this->addSql('ALTER TABLE page ALTER group_id DROP DEFAULT');

        $this->addSql('UPDATE page SET icon = \'fas fa-home\', alias = \'home\', group_id = 0 WHERE name = \'home\'');
        $this->addSql('UPDATE page SET icon = \'fas fa-tint\', alias = \'watering\', group_id = 1 WHERE name = \'watering\'');
        $this->addSql('UPDATE page SET icon = \'fab fa-free-code-camp fa-circle\', alias = \'fire-security\', group_id = 2 WHERE name = \'fire-security\'');
        $this->addSql('UPDATE page SET icon = \'fas fa-user-lock\', alias = \'security\', group_id = 3 WHERE name = \'security\'');
        $this->addSql('UPDATE page SET icon = \'fas fa-border-style\', alias = \'outbuilding\', group_id = 4 WHERE name = \'outbuilding\'');
    }

    public function down(Schema $schema): void
    {
    }
}
