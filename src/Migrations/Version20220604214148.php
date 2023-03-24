<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220604214148 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add custom fields for user';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "user" ADD is_verified BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD name VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6495E237E06 ON "user" (name)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_8D93D6495E237E06');
        $this->addSql('ALTER TABLE "user" DROP is_verified');
        $this->addSql('ALTER TABLE "user" DROP name');
    }
}
