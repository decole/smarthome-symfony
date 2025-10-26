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
final class Version20250923193846 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE fire_security ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE fire_security ALTER updated_at DROP NOT NULL');
        $this->addSql('ALTER TABLE page ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE page ALTER updated_at DROP NOT NULL');
        $this->addSql('ALTER TABLE plc ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE plc ALTER updated_at DROP NOT NULL');
        $this->addSql('ALTER TABLE relay ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE relay ALTER updated_at DROP NOT NULL');
        $this->addSql('ALTER TABLE schedule_task ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE schedule_task ALTER updated_at DROP NOT NULL');
        $this->addSql('ALTER TABLE security ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE security ALTER updated_at DROP NOT NULL');
        $this->addSql('ALTER TABLE sensor ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE sensor ALTER updated_at DROP NOT NULL');
        $this->addSql('ALTER TABLE visual_notify ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE visual_notify ALTER updated_at DROP NOT NULL');
        $this->addSql('ALTER TABLE fire_security ADD status_message_message_info VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fire_security ADD status_message_message_ok VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fire_security ADD status_message_message_warn VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE plc ADD status_message_message_info VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE plc ADD status_message_message_ok VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE plc ADD status_message_message_warn VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE relay ADD status_message_message_info VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE relay ADD status_message_message_ok VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE relay ADD status_message_message_warn VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE security ADD status_message_message_info VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE security ADD status_message_message_ok VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE security ADD status_message_message_warn VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE sensor ADD status_message_message_info VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE sensor ADD status_message_message_ok VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE sensor ADD status_message_message_warn VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE plc DROP status_message_message_info');
        $this->addSql('ALTER TABLE plc DROP status_message_message_ok');
        $this->addSql('ALTER TABLE plc DROP status_message_message_warn');
        $this->addSql('ALTER TABLE relay DROP status_message_message_info');
        $this->addSql('ALTER TABLE relay DROP status_message_message_ok');
        $this->addSql('ALTER TABLE relay DROP status_message_message_warn');
        $this->addSql('ALTER TABLE fire_security DROP status_message_message_info');
        $this->addSql('ALTER TABLE fire_security DROP status_message_message_ok');
        $this->addSql('ALTER TABLE fire_security DROP status_message_message_warn');
        $this->addSql('ALTER TABLE sensor DROP status_message_message_info');
        $this->addSql('ALTER TABLE sensor DROP status_message_message_ok');
        $this->addSql('ALTER TABLE sensor DROP status_message_message_warn');
        $this->addSql('ALTER TABLE security DROP status_message_message_info');
        $this->addSql('ALTER TABLE security DROP status_message_message_ok');
        $this->addSql('ALTER TABLE security DROP status_message_message_warn');
        $this->addSql('ALTER TABLE schedule_task ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE schedule_task ALTER updated_at SET NOT NULL');
        $this->addSql('ALTER TABLE plc ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE plc ALTER updated_at SET NOT NULL');
        $this->addSql('ALTER TABLE relay ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE relay ALTER updated_at SET NOT NULL');
        $this->addSql('ALTER TABLE fire_security ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE fire_security ALTER updated_at SET NOT NULL');
        $this->addSql('ALTER TABLE sensor ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE sensor ALTER updated_at SET NOT NULL');
        $this->addSql('ALTER TABLE visual_notify ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE visual_notify ALTER updated_at SET NOT NULL');
        $this->addSql('ALTER TABLE page ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE page ALTER updated_at SET NOT NULL');
        $this->addSql('ALTER TABLE security ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE security ALTER updated_at SET NOT NULL');
    }
}
