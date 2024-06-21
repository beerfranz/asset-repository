<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240621143914 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE assessment_plan ADD assessment_template_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE assessment_plan ADD CONSTRAINT FK_4F296671D3A5C28F FOREIGN KEY (assessment_template_id) REFERENCES assessment_template (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_4F296671D3A5C28F ON assessment_plan (assessment_template_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE assessment_plan DROP CONSTRAINT FK_4F296671D3A5C28F');
        $this->addSql('DROP INDEX IDX_4F296671D3A5C28F');
        $this->addSql('ALTER TABLE assessment_plan DROP assessment_template_id');
    }
}
