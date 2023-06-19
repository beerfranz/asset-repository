<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230619160227 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE asset_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE asset_audit_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE asset_definition_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE asset_definition_relation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE environment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE environment_definition_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE instance_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE kind_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE owner_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE registry_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE source_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE version_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE asset (id INT NOT NULL, owner_id INT DEFAULT NULL, source_id INT DEFAULT NULL, asset_definition_id INT DEFAULT NULL, version_id INT DEFAULT NULL, kind_id INT DEFAULT NULL, environment_id INT DEFAULT NULL, identifier VARCHAR(255) NOT NULL, attributes JSON DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_by VARCHAR(255) NOT NULL, labels JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2AF5A5C7E3C61F9 ON asset (owner_id)');
        $this->addSql('CREATE INDEX IDX_2AF5A5C953C1C61 ON asset (source_id)');
        $this->addSql('CREATE INDEX IDX_2AF5A5C126B7070 ON asset (asset_definition_id)');
        $this->addSql('CREATE INDEX IDX_2AF5A5C4BBC2705 ON asset (version_id)');
        $this->addSql('CREATE INDEX IDX_2AF5A5C30602CA9 ON asset (kind_id)');
        $this->addSql('CREATE INDEX IDX_2AF5A5C903E3A94 ON asset (environment_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2AF5A5C772E836A ON asset (identifier)');
        $this->addSql('COMMENT ON COLUMN asset.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE asset_audit (id INT NOT NULL, asset_id INT DEFAULT NULL, datetime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, actor VARCHAR(255) NOT NULL, action VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, data JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_743D13E5DA1941 ON asset_audit (asset_id)');
        $this->addSql('COMMENT ON COLUMN asset_audit.datetime IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE asset_definition (id INT NOT NULL, environment_definition_id INT DEFAULT NULL, owner_id INT DEFAULT NULL, source_id INT DEFAULT NULL, identifier VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, tags JSON DEFAULT NULL, labels JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AED1CC82C8B3E628 ON asset_definition (environment_definition_id)');
        $this->addSql('CREATE INDEX IDX_AED1CC827E3C61F9 ON asset_definition (owner_id)');
        $this->addSql('CREATE INDEX IDX_AED1CC82953C1C61 ON asset_definition (source_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AED1CC82772E836A ON asset_definition (identifier)');
        $this->addSql('CREATE TABLE asset_definition_relation (id INT NOT NULL, asset_definition_from_id INT NOT NULL, asset_definition_to_id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_74100F750A153 ON asset_definition_relation (asset_definition_from_id)');
        $this->addSql('CREATE INDEX IDX_74100F7889EB866 ON asset_definition_relation (asset_definition_to_id)');
        $this->addSql('CREATE TABLE environment (id INT NOT NULL, identifier VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4626DE22772E836A ON environment (identifier)');
        $this->addSql('CREATE TABLE environment_definition (id INT NOT NULL, identifier VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, attributes JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8A4A660F772E836A ON environment_definition (identifier)');
        $this->addSql('CREATE TABLE instance (id INT NOT NULL, asset_id INT DEFAULT NULL, source_id INT DEFAULT NULL, identifier VARCHAR(255) NOT NULL, attributes JSON DEFAULT NULL, version VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4230B1DE5DA1941 ON instance (asset_id)');
        $this->addSql('CREATE INDEX IDX_4230B1DE953C1C61 ON instance (source_id)');
        $this->addSql('CREATE TABLE kind (id INT NOT NULL, identifier VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3BC4BCD9772E836A ON kind (identifier)');
        $this->addSql('CREATE TABLE owner (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CF60E67C5E237E06 ON owner (name)');
        $this->addSql('CREATE TABLE registry (id INT NOT NULL, identifier VARCHAR(255) NOT NULL, attributes JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE source (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5F8A7F735E237E06 ON source (name)');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE TABLE version (id INT NOT NULL, asset_definition_id INT DEFAULT NULL, registry_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BF1CD3C3126B7070 ON version (asset_definition_id)');
        $this->addSql('CREATE INDEX IDX_BF1CD3C34CB707ED ON version (registry_id)');
        $this->addSql('ALTER TABLE asset ADD CONSTRAINT FK_2AF5A5C7E3C61F9 FOREIGN KEY (owner_id) REFERENCES owner (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE asset ADD CONSTRAINT FK_2AF5A5C953C1C61 FOREIGN KEY (source_id) REFERENCES source (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE asset ADD CONSTRAINT FK_2AF5A5C126B7070 FOREIGN KEY (asset_definition_id) REFERENCES asset_definition (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE asset ADD CONSTRAINT FK_2AF5A5C4BBC2705 FOREIGN KEY (version_id) REFERENCES version (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE asset ADD CONSTRAINT FK_2AF5A5C30602CA9 FOREIGN KEY (kind_id) REFERENCES kind (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE asset ADD CONSTRAINT FK_2AF5A5C903E3A94 FOREIGN KEY (environment_id) REFERENCES environment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE asset_audit ADD CONSTRAINT FK_743D13E5DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE asset_definition ADD CONSTRAINT FK_AED1CC82C8B3E628 FOREIGN KEY (environment_definition_id) REFERENCES environment_definition (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE asset_definition ADD CONSTRAINT FK_AED1CC827E3C61F9 FOREIGN KEY (owner_id) REFERENCES owner (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE asset_definition ADD CONSTRAINT FK_AED1CC82953C1C61 FOREIGN KEY (source_id) REFERENCES source (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE asset_definition_relation ADD CONSTRAINT FK_74100F750A153 FOREIGN KEY (asset_definition_from_id) REFERENCES asset_definition (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE asset_definition_relation ADD CONSTRAINT FK_74100F7889EB866 FOREIGN KEY (asset_definition_to_id) REFERENCES asset_definition (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE instance ADD CONSTRAINT FK_4230B1DE5DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE instance ADD CONSTRAINT FK_4230B1DE953C1C61 FOREIGN KEY (source_id) REFERENCES source (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE version ADD CONSTRAINT FK_BF1CD3C3126B7070 FOREIGN KEY (asset_definition_id) REFERENCES asset_definition (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE version ADD CONSTRAINT FK_BF1CD3C34CB707ED FOREIGN KEY (registry_id) REFERENCES registry (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE asset_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE asset_audit_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE asset_definition_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE asset_definition_relation_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE environment_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE environment_definition_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE instance_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE kind_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE owner_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE registry_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE source_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE version_id_seq CASCADE');
        $this->addSql('ALTER TABLE asset DROP CONSTRAINT FK_2AF5A5C7E3C61F9');
        $this->addSql('ALTER TABLE asset DROP CONSTRAINT FK_2AF5A5C953C1C61');
        $this->addSql('ALTER TABLE asset DROP CONSTRAINT FK_2AF5A5C126B7070');
        $this->addSql('ALTER TABLE asset DROP CONSTRAINT FK_2AF5A5C4BBC2705');
        $this->addSql('ALTER TABLE asset DROP CONSTRAINT FK_2AF5A5C30602CA9');
        $this->addSql('ALTER TABLE asset DROP CONSTRAINT FK_2AF5A5C903E3A94');
        $this->addSql('ALTER TABLE asset_audit DROP CONSTRAINT FK_743D13E5DA1941');
        $this->addSql('ALTER TABLE asset_definition DROP CONSTRAINT FK_AED1CC82C8B3E628');
        $this->addSql('ALTER TABLE asset_definition DROP CONSTRAINT FK_AED1CC827E3C61F9');
        $this->addSql('ALTER TABLE asset_definition DROP CONSTRAINT FK_AED1CC82953C1C61');
        $this->addSql('ALTER TABLE asset_definition_relation DROP CONSTRAINT FK_74100F750A153');
        $this->addSql('ALTER TABLE asset_definition_relation DROP CONSTRAINT FK_74100F7889EB866');
        $this->addSql('ALTER TABLE instance DROP CONSTRAINT FK_4230B1DE5DA1941');
        $this->addSql('ALTER TABLE instance DROP CONSTRAINT FK_4230B1DE953C1C61');
        $this->addSql('ALTER TABLE version DROP CONSTRAINT FK_BF1CD3C3126B7070');
        $this->addSql('ALTER TABLE version DROP CONSTRAINT FK_BF1CD3C34CB707ED');
        $this->addSql('DROP TABLE asset');
        $this->addSql('DROP TABLE asset_audit');
        $this->addSql('DROP TABLE asset_definition');
        $this->addSql('DROP TABLE asset_definition_relation');
        $this->addSql('DROP TABLE environment');
        $this->addSql('DROP TABLE environment_definition');
        $this->addSql('DROP TABLE instance');
        $this->addSql('DROP TABLE kind');
        $this->addSql('DROP TABLE owner');
        $this->addSql('DROP TABLE registry');
        $this->addSql('DROP TABLE source');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE version');
    }
}
