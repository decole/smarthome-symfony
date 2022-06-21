<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220611180604 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Изменение типа хранения статуса сенсоров';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sensor ALTER status TYPE SMALLINT USING CASE WHEN status is TRUE THEN 1 ELSE 0 END;');
        $this->addSql('ALTER TABLE sensor ALTER status DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE sensor ALTER status TYPE BOOLEAN USING CASE WHEN status=0 THEN FALSE ELSE TRUE END;');
        $this->addSql('ALTER TABLE sensor ALTER status DROP DEFAULT');
    }
}
