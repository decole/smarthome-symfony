<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220813152001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Создание БД для системной пожарной безопасности';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fire_security (id UUID NOT NULL, name VARCHAR(255) NOT NULL, topic VARCHAR(255) NOT NULL, payload VARCHAR(255) DEFAULT NULL, normal_payload VARCHAR(255) NOT NULL, alert_payload VARCHAR(255) NOT NULL, last_command VARCHAR(255) DEFAULT NULL, status SMALLINT NOT NULL, notify BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, message_info VARCHAR(255) DEFAULT NULL, message_ok VARCHAR(255) DEFAULT NULL, message_warn VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_884BB5915E237E06 ON fire_security (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_884BB5919D40DE1B ON fire_security (topic)');
        $this->addSql('COMMENT ON COLUMN fire_security.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN fire_security.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN fire_security.updated_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE fire_security');
    }
}
