<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240324211404 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE risk_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE risk_manager_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE risk (id INT NOT NULL, risk_manager_id INT NOT NULL, asset_id INT NOT NULL, identifier VARCHAR(255) NOT NULL, values JSON NOT NULL, mitigations JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7906D541758EB508 ON risk (risk_manager_id)');
        $this->addSql('CREATE INDEX IDX_7906D5415DA1941 ON risk (asset_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7906D541772E836A ON risk (identifier)');
        $this->addSql('CREATE TABLE risk_manager (id INT NOT NULL, identifier VARCHAR(255) NOT NULL, values JSON NOT NULL, values_aggregator VARCHAR(255) NOT NULL, triggers JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B03B4BF6772E836A ON risk_manager (identifier)');
        $this->addSql('ALTER TABLE risk ADD CONSTRAINT FK_7906D541758EB508 FOREIGN KEY (risk_manager_id) REFERENCES risk_manager (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE risk ADD CONSTRAINT FK_7906D5415DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE risk ADD description TEXT DEFAULT NULL');
        $this->addSql('CREATE SEQUENCE task_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE task_event_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE task (id INT NOT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, is_done BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, assigned_to VARCHAR(255) DEFAULT NULL, owner VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN task.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_527EDB25772E836A ON task (identifier)');
        $this->addSql('CREATE TABLE task_event (id INT NOT NULL, task_id INT NOT NULL, datetime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, author VARCHAR(255) NOT NULL, description TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FC0A11878DB60186 ON task_event (task_id)');
        $this->addSql('COMMENT ON COLUMN task_event.datetime IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE task_event ADD CONSTRAINT FK_FC0A11878DB60186 FOREIGN KEY (task_id) REFERENCES task (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE SEQUENCE task_template_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE task_template (id INT NOT NULL, identifier VARCHAR(255) NOT NULL, title VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, generate_task_automatically VARCHAR(255) DEFAULT NULL, frequency JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE SEQUENCE indicator_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE indicator_value_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE indicator (id INT NOT NULL, identifier VARCHAR(255) NOT NULL, namespace VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, target_value INT DEFAULT NULL, triggers JSON DEFAULT NULL, is_activated BOOLEAN NOT NULL, frequency JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D1349DB3772E836A ON indicator (identifier)');
        $this->addSql('CREATE TABLE indicator_value (id INT NOT NULL, indicator_id INT NOT NULL, datetime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_validated BOOLEAN NOT NULL, validator VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D18506234402854A ON indicator_value (indicator_id)');
        $this->addSql('COMMENT ON COLUMN indicator_value.datetime IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE indicator_value ADD CONSTRAINT FK_D18506234402854A FOREIGN KEY (indicator_id) REFERENCES indicator (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D7A0F5CF772E836A ON task_template (identifier)');
        $this->addSql('ALTER TABLE indicator_value ADD value INT NOT NULL');
        $this->addSql('ALTER TABLE indicator_value ADD identifier VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE task ADD task_template_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE task ADD identifier VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB2543AFA28A FOREIGN KEY (task_template_id) REFERENCES task_template (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_527EDB2543AFA28A ON task (task_template_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX IDX_527EDB2543AFA28A');
        $this->addSql('DROP SEQUENCE indicator_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE indicator_value_id_seq CASCADE');
        $this->addSql('DROP TABLE indicator');
        $this->addSql('DROP TABLE indicator_value');
        $this->addSql('DROP INDEX UNIQ_D7A0F5CF772E836A');
        $this->addSql('DROP SEQUENCE task_template_id_seq CASCADE');
        $this->addSql('DROP TABLE task_template');
        $this->addSql('DROP SEQUENCE task_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE task_event_id_seq CASCADE');
        $this->addSql('DROP INDEX UNIQ_527EDB25772E836A');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE task_event');
        $this->addSql('DROP SEQUENCE risk_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE risk_manager_id_seq CASCADE');
        $this->addSql('DROP TABLE risk');
        $this->addSql('DROP TABLE risk_manager');
    }
}
