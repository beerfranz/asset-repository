<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240512085433 extends AbstractMigration
{

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE setting_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE setting (id INT NOT NULL, identifier VARCHAR(255) NOT NULL, value JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX setting_identifier_idx ON setting (identifier)');
        $this->addSql('CREATE INDEX audit_subject_kind_idx ON audit (subject_kind)');
        $this->addSql('CREATE INDEX audit_subject_idx ON audit (subject)');
        $this->addSql('CREATE INDEX audit_actor_idx ON audit (actor)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE configuration_id_seq CASCADE');
        $this->addSql('DROP TABLE setting');
        $this->addSql('DROP INDEX audit_subject_kind_idx');
        $this->addSql('DROP INDEX audit_subject_idx');
        $this->addSql('DROP INDEX audit_actor_idx');
    }
}
