<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230706212329 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE asset_definition_relation ADD source_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE asset_definition_relation ADD CONSTRAINT FK_74100F7953C1C61 FOREIGN KEY (source_id) REFERENCES source (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_74100F7953C1C61 ON asset_definition_relation (source_id)');
        $this->addSql('CREATE SEQUENCE relation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE relation (id INT NOT NULL, source_id INT DEFAULT NULL, from_asset_id INT DEFAULT NULL, to_asset_id INT DEFAULT NULL, kind VARCHAR(255) NOT NULL, attributes JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_62894749953C1C61 ON relation (source_id)');
        $this->addSql('CREATE INDEX IDX_628947494335F9DB ON relation (from_asset_id)');
        $this->addSql('CREATE INDEX IDX_62894749C88387EF ON relation (to_asset_id)');
        $this->addSql('ALTER TABLE relation ADD CONSTRAINT FK_62894749953C1C61 FOREIGN KEY (source_id) REFERENCES source (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE relation ADD CONSTRAINT FK_628947494335F9DB FOREIGN KEY (from_asset_id) REFERENCES asset (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE relation ADD CONSTRAINT FK_62894749C88387EF FOREIGN KEY (to_asset_id) REFERENCES asset (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE asset ADD parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE asset ADD CONSTRAINT FK_2AF5A5C727ACA70 FOREIGN KEY (parent_id) REFERENCES asset (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_2AF5A5C727ACA70 ON asset (parent_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE asset_definition_relation DROP CONSTRAINT FK_74100F7953C1C61');
        $this->addSql('DROP INDEX IDX_74100F7953C1C61');
        $this->addSql('ALTER TABLE asset_definition_relation DROP source_id');
        $this->addSql('DROP SEQUENCE relation_id_seq CASCADE');
        $this->addSql('ALTER TABLE relation DROP CONSTRAINT FK_62894749953C1C61');
        $this->addSql('ALTER TABLE relation DROP CONSTRAINT FK_628947494335F9DB');
        $this->addSql('ALTER TABLE relation DROP CONSTRAINT FK_62894749C88387EF');
        $this->addSql('DROP TABLE relation');
        $this->addSql('ALTER TABLE asset DROP CONSTRAINT FK_2AF5A5C727ACA70');
        $this->addSql('DROP INDEX IDX_2AF5A5C727ACA70');
        $this->addSql('ALTER TABLE asset DROP parent_id');
    }
}
