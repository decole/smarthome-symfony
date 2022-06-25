<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220622195649 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Создание БД для разных видов реле';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE relay (id UUID NOT NULL, name VARCHAR(255) NOT NULL, topic VARCHAR(255) NOT NULL, check_topic VARCHAR(255) DEFAULT NULL, command_on VARCHAR(255) NOT NULL, command_off VARCHAR(255) NOT NULL, check_topic_payload_on VARCHAR(255) DEFAULT NULL, check_topic_payload_off VARCHAR(255) DEFAULT NULL, is_feedback_payload BOOLEAN NOT NULL, payload VARCHAR(255) DEFAULT NULL, last_command VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, status SMALLINT NOT NULL, notify BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, message_info VARCHAR(255) DEFAULT NULL, message_ok VARCHAR(255) DEFAULT NULL, message_warn VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5D3AE2B95E237E06 ON relay (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5D3AE2B99D40DE1B ON relay (topic)');
        $this->addSql('COMMENT ON COLUMN relay.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN relay.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN relay.updated_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE relay');
    }
}
