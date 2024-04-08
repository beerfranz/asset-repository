<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240404123134 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE task_workflow_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE task_workflow (id INT NOT NULL, identifier VARCHAR(255) NOT NULL, workflow JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2505725A772E836A ON task_workflow (identifier)');
        $this->addSql('COMMENT ON COLUMN task_workflow.workflow IS \'(DC2Type:json_document)\'');
        $this->addSql('ALTER TABLE indicator ALTER triggers TYPE JSON');
        $this->addSql('ALTER TABLE indicator ALTER frequency TYPE JSON');
        $this->addSql('COMMENT ON COLUMN indicator.triggers IS \'(DC2Type:json_document)\'');
        $this->addSql('COMMENT ON COLUMN indicator.frequency IS \'(DC2Type:json_document)\'');
        $this->addSql('ALTER TABLE indicator_value ALTER trigger TYPE JSON');
        $this->addSql('COMMENT ON COLUMN indicator_value.trigger IS \'(DC2Type:json_document)\'');
        $this->addSql('ALTER TABLE task ADD parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE task ADD status VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25727ACA70 FOREIGN KEY (parent_id) REFERENCES task (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_527EDB25727ACA70 ON task (parent_id)');
        $this->addSql('ALTER TABLE task_template ADD task_workflow_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE task_template ALTER frequency TYPE JSON');
        $this->addSql('COMMENT ON COLUMN task_template.frequency IS \'(DC2Type:json_document)\'');
        $this->addSql('ALTER TABLE task_template ADD CONSTRAINT FK_D7A0F5CF6A098188 FOREIGN KEY (task_workflow_id) REFERENCES task_workflow (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D7A0F5CF6A098188 ON task_template (task_workflow_id)');

        $this->addSql('CREATE SEQUENCE task_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE task_type (id INT NOT NULL, task_workflow_id INT DEFAULT NULL, identifier VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FF6DC3526A098188 ON task_type (task_workflow_id)');
        $this->addSql('ALTER TABLE task_type ADD CONSTRAINT FK_FF6DC3526A098188 FOREIGN KEY (task_workflow_id) REFERENCES task_workflow (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task ADD task_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25DAADA679 FOREIGN KEY (task_type_id) REFERENCES task_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_527EDB25DAADA679 ON task (task_type_id)');
        $this->addSql('ALTER TABLE task_template DROP CONSTRAINT fk_d7a0f5cf6a098188');
        $this->addSql('DROP INDEX idx_d7a0f5cf6a098188');
        $this->addSql('ALTER TABLE task_template ADD task_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE task_template RENAME COLUMN task_workflow_id TO parent_id');
        $this->addSql('ALTER TABLE task_template ADD CONSTRAINT FK_D7A0F5CF727ACA70 FOREIGN KEY (parent_id) REFERENCES task_template (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_template ADD CONSTRAINT FK_D7A0F5CFDAADA679 FOREIGN KEY (task_type_id) REFERENCES task_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D7A0F5CF727ACA70 ON task_template (parent_id)');
        $this->addSql('CREATE INDEX IDX_D7A0F5CFDAADA679 ON task_template (task_type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE task_type_id_seq CASCADE');
        $this->addSql('DROP TABLE task_type');
        $this->addSql('DROP INDEX IDX_527EDB25DAADA679');
        $this->addSql('ALTER TABLE task DROP task_type_id');
        $this->addSql('ALTER TABLE task_template DROP CONSTRAINT FK_D7A0F5CF727ACA70');
        $this->addSql('DROP INDEX IDX_D7A0F5CF727ACA70');
        $this->addSql('DROP INDEX IDX_D7A0F5CFDAADA679');
        $this->addSql('ALTER TABLE task_template DROP parent_id');
        $this->addSql('ALTER TABLE task_template DROP task_type_id');
        $this->addSql('ALTER TABLE task_template ADD CONSTRAINT fk_d7a0f5cf6a098188 FOREIGN KEY (task_workflow_id) REFERENCES task_workflow (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_d7a0f5cf6a098188 ON task_template (task_workflow_id)');
        $this->addSql('ALTER TABLE task_template DROP CONSTRAINT FK_D7A0F5CF6A098188');
        $this->addSql('DROP SEQUENCE task_workflow_id_seq CASCADE');
        $this->addSql('DROP TABLE task_workflow');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB25727ACA70');
        $this->addSql('DROP INDEX IDX_527EDB25727ACA70');
        $this->addSql('ALTER TABLE task DROP parent_id');
        $this->addSql('ALTER TABLE task DROP status');
        $this->addSql('ALTER TABLE indicator ALTER triggers TYPE JSON');
        $this->addSql('ALTER TABLE indicator ALTER frequency TYPE JSON');
        $this->addSql('COMMENT ON COLUMN indicator.triggers IS NULL');
        $this->addSql('COMMENT ON COLUMN indicator.frequency IS NULL');
        $this->addSql('ALTER TABLE indicator_value ALTER trigger TYPE JSON');
        $this->addSql('COMMENT ON COLUMN indicator_value.trigger IS NULL');
        $this->addSql('DROP INDEX IDX_D7A0F5CF6A098188');
        $this->addSql('ALTER TABLE task_template DROP task_workflow_id');
        $this->addSql('ALTER TABLE task_template ALTER frequency TYPE JSON');
        $this->addSql('COMMENT ON COLUMN task_template.frequency IS NULL');
    }
}
