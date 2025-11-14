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
final class Version20250917140909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE app_user (name VARCHAR(255) NOT NULL, email VARCHAR(180) DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified BOOLEAN NOT NULL, telegram_id INT DEFAULT NULL, restore_token VARCHAR(255) DEFAULT NULL, restore_token_created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, google_auth_secret VARCHAR(255) DEFAULT NULL, id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE fire_security (name VARCHAR(255) NOT NULL, topic VARCHAR(255) NOT NULL, payload VARCHAR(255) DEFAULT NULL, normal_payload VARCHAR(255) DEFAULT NULL, alert_payload VARCHAR(255) DEFAULT NULL, last_command VARCHAR(255) DEFAULT NULL, status SMALLINT NOT NULL, notify BOOLEAN NOT NULL, id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_884BB5915E237E06 ON fire_security (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_884BB5919D40DE1B ON fire_security (topic)');
        $this->addSql('CREATE TABLE page (name VARCHAR(255) NOT NULL, config JSON NOT NULL, icon VARCHAR(255) NOT NULL, alias VARCHAR(255) NOT NULL, group_id INT NOT NULL, id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_140AB6205E237E06 ON page (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_140AB620E16C6B94 ON page (alias)');
        $this->addSql('CREATE TABLE plc (name VARCHAR(255) NOT NULL, target_topic VARCHAR(255) NOT NULL, alarm_second_delay INT NOT NULL, status SMALLINT NOT NULL, notify BOOLEAN NOT NULL, id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B643A50B5E237E06 ON plc (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B643A50B21EB4517 ON plc (target_topic)');
        $this->addSql('CREATE TABLE relay (type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, topic VARCHAR(255) NOT NULL, payload VARCHAR(255) DEFAULT NULL, command_on VARCHAR(255) NOT NULL, command_off VARCHAR(255) NOT NULL, check_topic VARCHAR(255) DEFAULT NULL, check_topic_payload_on VARCHAR(255) DEFAULT NULL, check_topic_payload_off VARCHAR(255) DEFAULT NULL, last_command VARCHAR(255) DEFAULT NULL, is_feedback_payload BOOLEAN NOT NULL, status SMALLINT NOT NULL, notify BOOLEAN NOT NULL, id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5D3AE2B95E237E06 ON relay (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5D3AE2B99D40DE1B ON relay (topic)');
        $this->addSql('CREATE TABLE schedule_task (command VARCHAR(255) NOT NULL, arguments JSON NOT NULL, interval VARCHAR(255) DEFAULT NULL, last_run TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, next_run TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B236FB7E8ECAEAD4 ON schedule_task (command)');
        $this->addSql('CREATE TABLE security (security_type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, topic VARCHAR(255) NOT NULL, payload VARCHAR(255) DEFAULT NULL, detect_payload VARCHAR(255) NOT NULL, hold_payload VARCHAR(255) NOT NULL, last_command VARCHAR(255) DEFAULT NULL, params JSON NOT NULL, status SMALLINT NOT NULL, notify BOOLEAN NOT NULL, id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C59BD5C15E237E06 ON security (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C59BD5C19D40DE1B ON security (topic)');
        $this->addSql('CREATE TABLE sensor (name VARCHAR(255) NOT NULL, topic VARCHAR(255) NOT NULL, payload VARCHAR(255) DEFAULT NULL, status SMALLINT NOT NULL, notify BOOLEAN NOT NULL, id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, sensor_type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BC8617B05E237E06 ON sensor (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BC8617B09D40DE1B ON sensor (topic)');
        $this->addSql('CREATE TABLE sensor_humidity (payload_min VARCHAR(255) DEFAULT NULL, payload_max VARCHAR(255) DEFAULT NULL, id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE sensor_leakage (payload_dry VARCHAR(255) DEFAULT NULL, payload_wet VARCHAR(255) DEFAULT NULL, id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE sensor_pressure (payload_min VARCHAR(255) DEFAULT NULL, payload_max VARCHAR(255) DEFAULT NULL, id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE sensor_temperature (payload_min VARCHAR(255) DEFAULT NULL, payload_max VARCHAR(255) DEFAULT NULL, id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE visual_notify (is_read BOOLEAN NOT NULL, type SMALLINT NOT NULL, message VARCHAR(500) NOT NULL, id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE sensor_humidity ADD CONSTRAINT FK_FFE8C6CBBF396750 FOREIGN KEY (id) REFERENCES sensor (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sensor_leakage ADD CONSTRAINT FK_825E0713BF396750 FOREIGN KEY (id) REFERENCES sensor (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sensor_pressure ADD CONSTRAINT FK_C9BB116EBF396750 FOREIGN KEY (id) REFERENCES sensor (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sensor_temperature ADD CONSTRAINT FK_569B5AF1BF396750 FOREIGN KEY (id) REFERENCES sensor (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE TABLE sensor_dry_contact (payload_high VARCHAR(255) DEFAULT NULL, payload_low VARCHAR(255) DEFAULT NULL, id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE sensor_dry_contact ADD CONSTRAINT FK_90EBDCCCBF396750 FOREIGN KEY (id) REFERENCES sensor (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sensor_humidity DROP CONSTRAINT FK_FFE8C6CBBF396750');
        $this->addSql('ALTER TABLE sensor_leakage DROP CONSTRAINT FK_825E0713BF396750');
        $this->addSql('ALTER TABLE sensor_pressure DROP CONSTRAINT FK_C9BB116EBF396750');
        $this->addSql('ALTER TABLE sensor_temperature DROP CONSTRAINT FK_569B5AF1BF396750');
        $this->addSql('ALTER TABLE sensor_dry_contact DROP CONSTRAINT FK_90EBDCCCBF396750');
        $this->addSql('DROP TABLE app_user');
        $this->addSql('DROP TABLE fire_security');
        $this->addSql('DROP TABLE page');
        $this->addSql('DROP TABLE plc');
        $this->addSql('DROP TABLE relay');
        $this->addSql('DROP TABLE schedule_task');
        $this->addSql('DROP TABLE security');
        $this->addSql('DROP TABLE sensor');
        $this->addSql('DROP TABLE sensor_humidity');
        $this->addSql('DROP TABLE sensor_leakage');
        $this->addSql('DROP TABLE sensor_pressure');
        $this->addSql('DROP TABLE sensor_temperature');
        $this->addSql('DROP TABLE sensor_dry_contact');
        $this->addSql('DROP TABLE visual_notify');
    }
}
