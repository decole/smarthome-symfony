<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220626092000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Создание БД для системной безопасности';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE security (id UUID NOT NULL, security_type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, topic VARCHAR(255) NOT NULL, payload VARCHAR(255) DEFAULT NULL, detect_payload VARCHAR(255) NOT NULL, hold_payload VARCHAR(255) NOT NULL, last_command VARCHAR(255) DEFAULT NULL, params JSON NOT NULL, status SMALLINT NOT NULL, notify BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, message_info VARCHAR(255) DEFAULT NULL, message_ok VARCHAR(255) DEFAULT NULL, message_warn VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C59BD5C15E237E06 ON security (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C59BD5C19D40DE1B ON security (topic)');
        $this->addSql('COMMENT ON COLUMN security.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN security.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN security.updated_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE security');
    }
}
