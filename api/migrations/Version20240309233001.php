<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240309233001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE asset ADD description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE asset ADD links JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE asset ADD rules JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE instance ADD is_conform BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE instance ADD conformities JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE asset DROP description');
        $this->addSql('ALTER TABLE asset DROP links');
        $this->addSql('ALTER TABLE asset DROP rules');
        $this->addSql('ALTER TABLE instance DROP is_conform');
        $this->addSql('ALTER TABLE instance DROP conformities');
    }
}
