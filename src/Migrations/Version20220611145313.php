<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220611145313 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Создание БД для разных видов сенсоров';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE sensor_dry_contact (id UUID NOT NULL, payload_high VARCHAR(255) NOT NULL, payload_low VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN sensor_dry_contact.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE sensor_humidity (id UUID NOT NULL, payload_min VARCHAR(255) NOT NULL, payload_max VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN sensor_humidity.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE sensor_leakage (id UUID NOT NULL, payload_dry VARCHAR(255) NOT NULL, payload_wet VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN sensor_leakage.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE sensor_pressure (id UUID NOT NULL, payload_min VARCHAR(255) NOT NULL, payload_max VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN sensor_pressure.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE sensor_temperature (id UUID NOT NULL, payload_min VARCHAR(255) NOT NULL, payload_max VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN sensor_temperature.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE sensor_dry_contact ADD CONSTRAINT FK_90EBDCCCBF396750 FOREIGN KEY (id) REFERENCES sensor (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sensor_humidity ADD CONSTRAINT FK_FFE8C6CBBF396750 FOREIGN KEY (id) REFERENCES sensor (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sensor_leakage ADD CONSTRAINT FK_825E0713BF396750 FOREIGN KEY (id) REFERENCES sensor (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sensor_pressure ADD CONSTRAINT FK_C9BB116EBF396750 FOREIGN KEY (id) REFERENCES sensor (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sensor_temperature ADD CONSTRAINT FK_569B5AF1BF396750 FOREIGN KEY (id) REFERENCES sensor (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sensor ADD sensor_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE sensor DROP payload_min');
        $this->addSql('ALTER TABLE sensor DROP payload_max');
        $this->addSql('ALTER TABLE sensor DROP type');
        $this->addSql('ALTER TABLE sensor ALTER status DROP DEFAULT');
        $this->addSql('ALTER TABLE sensor ALTER status TYPE BOOLEAN USING CASE WHEN status=0 THEN FALSE ELSE TRUE END;');
        $this->addSql('ALTER TABLE sensor ALTER status DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE sensor_dry_contact');
        $this->addSql('DROP TABLE sensor_humidity');
        $this->addSql('DROP TABLE sensor_leakage');
        $this->addSql('DROP TABLE sensor_pressure');
        $this->addSql('DROP TABLE sensor_temperature');
        $this->addSql('ALTER TABLE sensor ADD payload_min INT NOT NULL');
        $this->addSql('ALTER TABLE sensor ADD payload_max INT NOT NULL');
        $this->addSql('ALTER TABLE sensor ADD type SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE sensor DROP sensor_type');
        $this->addSql('ALTER TABLE sensor ALTER status DROP DEFAULT');
        $this->addSql('ALTER TABLE sensor ALTER status TYPE SMALLINT USING CASE WHEN status is TRUE THEN 1 ELSE 0 END;');
        $this->addSql('ALTER TABLE sensor ALTER status DROP DEFAULT');
    }
}
