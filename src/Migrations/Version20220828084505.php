<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220828084505 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'добавлена таблица страниц сайта';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE page (id UUID NOT NULL, name VARCHAR(255) NOT NULL, config JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_140AB6205E237E06 ON page (name)');
        $this->addSql('COMMENT ON COLUMN page.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN page.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN page.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN page.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql("INSERT INTO page (id, name, config, created_at, updated_at) VALUES ('3fa6307c-b81c-48d4-af7c-26f6d8815750', 'home', '{\"sensor\":[],\"relay\":[],\"security\":[],\"fireSecurity\":[]}', '2022-09-08 11:36:49', null);");
        $this->addSql("INSERT INTO page (id, name, config, created_at, updated_at) VALUES ('71694b0d-2459-4e80-b28b-77408e274b1e', 'watering', '{\"sensor\":[],\"relay\":[],\"security\":[],\"fireSecurity\":[]}', '2022-09-08 11:36:59', null);");
        $this->addSql("INSERT INTO page (id, name, config, created_at, updated_at) VALUES ('932aa8aa-b4ee-4ca6-9c36-050e5ce52d3b', 'fire-security', '{\"sensor\":[],\"relay\":[],\"security\":[],\"fireSecurity\":[]}', '2022-09-08 11:37:09', null);");
        $this->addSql("INSERT INTO page (id, name, config, created_at, updated_at) VALUES ('aa0fc486-bd72-4160-8e50-1d625c7639ea', 'security', '{\"sensor\":[],\"relay\":[],\"security\":[],\"fireSecurity\":[]}', '2022-09-08 11:37:17', null);");
        $this->addSql("INSERT INTO page (id, name, config, created_at, updated_at) VALUES ('b5c34501-6165-4c5c-ad4a-cd8be254fb05', 'outbuilding', '{\"sensor\":[],\"relay\":[],\"security\":[],\"fireSecurity\":[]}', '2022-09-08 11:37:31', null);");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE page');
    }
}
