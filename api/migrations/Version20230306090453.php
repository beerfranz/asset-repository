<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230306090453 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE greeting_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE asset_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE asset_audit_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE asset_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE asset (id INT NOT NULL, type_id INT DEFAULT NULL, identifier VARCHAR(255) NOT NULL, attributes JSON DEFAULT NULL, owner VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_by VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2AF5A5CC54C8C93 ON asset (type_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2AF5A5C772E836A ON asset (identifier)');
        $this->addSql('COMMENT ON COLUMN asset.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE asset_audit (id INT NOT NULL, asset_id INT DEFAULT NULL, datetime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, actor VARCHAR(255) NOT NULL, action VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, data JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_743D13E5DA1941 ON asset_audit (asset_id)');
        $this->addSql('COMMENT ON COLUMN asset_audit.datetime IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE asset_type (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_68BA92E15E237E06 ON asset_type (name)');
        $this->addSql('ALTER TABLE asset ADD CONSTRAINT FK_2AF5A5CC54C8C93 FOREIGN KEY (type_id) REFERENCES asset_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE asset_audit ADD CONSTRAINT FK_743D13E5DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE greeting');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE asset_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE asset_audit_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE asset_type_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE greeting_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE greeting (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE asset DROP CONSTRAINT FK_2AF5A5CC54C8C93');
        $this->addSql('ALTER TABLE asset_audit DROP CONSTRAINT FK_743D13E5DA1941');
        $this->addSql('DROP TABLE asset');
        $this->addSql('DROP TABLE asset_audit');
        $this->addSql('DROP TABLE asset_type');
    }
}
