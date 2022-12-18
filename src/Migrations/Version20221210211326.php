<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221210211326 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE plc (id UUID NOT NULL, name VARCHAR(255) NOT NULL, target_topic VARCHAR(255) NOT NULL, alarm_second_delay INT NOT NULL, topics JSON NOT NULL, status SMALLINT NOT NULL, notify BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, message_info VARCHAR(255) DEFAULT NULL, message_ok VARCHAR(255) DEFAULT NULL, message_warn VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B643A50B5E237E06 ON plc (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B643A50B21EB4517 ON plc (target_topic)');
        $this->addSql('COMMENT ON COLUMN plc.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN plc.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN plc.updated_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE plc');
    }
}
