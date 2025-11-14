<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250923190837 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO page (id, name, alias,  icon, config, created_at, updated_at, group_id) VALUES ('3fa6307c-b81c-48d4-af7c-26f6d8815750', 'home', 'home', 'fas fa-home','{\"sensor\":[],\"relay\":[],\"security\":[],\"fireSecurity\":[]}', '2022-09-08 11:36:49', '2022-09-08 11:36:49', 0)");
        $this->addSql("INSERT INTO page (id, name, alias, icon, config, created_at, updated_at, group_id) VALUES ('71694b0d-2459-4e80-b28b-77408e274b1e', 'watering', 'watering', 'fas fa-tint', '{\"sensor\":[],\"relay\":[],\"security\":[],\"fireSecurity\":[]}', '2022-09-08 11:36:59', '2022-09-08 11:36:59', 1)");
        $this->addSql("INSERT INTO page (id, name, alias, icon, config, created_at, updated_at, group_id) VALUES ('932aa8aa-b4ee-4ca6-9c36-050e5ce52d3b', 'fire-security', 'fire-security', 'fab fa-free-code-camp fa-circle', '{\"sensor\":[],\"relay\":[],\"security\":[],\"fireSecurity\":[]}', '2022-09-08 11:37:09', '2022-09-08 11:36:59', 2)");
        $this->addSql("INSERT INTO page (id, name, alias, icon, config, created_at, updated_at, group_id) VALUES ('aa0fc486-bd72-4160-8e50-1d625c7639ea', 'security', 'security', 'fas fa-user-lock', '{\"sensor\":[],\"relay\":[],\"security\":[],\"fireSecurity\":[]}', '2022-09-08 11:37:17', '2022-09-08 11:36:59', 3)");
        $this->addSql("INSERT INTO page (id, name, alias, icon, config, created_at, updated_at, group_id) VALUES ('b5c34501-6165-4c5c-ad4a-cd8be254fb05', 'outbuilding', 'outbuilding', 'fas fa-border-style', '{\"sensor\":[],\"relay\":[],\"security\":[],\"fireSecurity\":[]}', '2022-09-08 11:37:31', '2022-09-08 11:36:59', 4)");
    }

    public function down(Schema $schema): void {}
}
