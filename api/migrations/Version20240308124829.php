<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240308124829 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE instance ADD friendly_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE instance ADD kind_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE instance ADD CONSTRAINT FK_4230B1DE30602CA9 FOREIGN KEY (kind_id) REFERENCES kind (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_4230B1DE30602CA9 ON instance (kind_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE instance DROP CONSTRAINT FK_4230B1DE30602CA9');
        $this->addSql('DROP INDEX IDX_4230B1DE30602CA9');
        $this->addSql('ALTER TABLE instance DROP kind_id');
        $this->addSql('ALTER TABLE instance DROP friendly_name');
    }
}
