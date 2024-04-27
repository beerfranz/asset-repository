<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240415055638 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE indicator ADD task_template_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE indicator ADD CONSTRAINT FK_D1349DB343AFA28A FOREIGN KEY (task_template_id) REFERENCES task_template (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D1349DB343AFA28A ON indicator (task_template_id)');
        $this->addSql('ALTER TABLE indicator_value ADD task_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE indicator_value ADD CONSTRAINT FK_D18506238DB60186 FOREIGN KEY (task_id) REFERENCES task (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D18506238DB60186 ON indicator_value (task_id)');
        $this->addSql('ALTER TABLE task ADD attributes JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE task_template ADD attributes JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE indicator DROP CONSTRAINT FK_D1349DB343AFA28A');
        $this->addSql('DROP INDEX IDX_D1349DB343AFA28A');
        $this->addSql('ALTER TABLE indicator DROP task_template_id');
        $this->addSql('ALTER TABLE task_template DROP attributes');
        $this->addSql('ALTER TABLE indicator_value DROP CONSTRAINT FK_D18506238DB60186');
        $this->addSql('DROP INDEX IDX_D18506238DB60186');
        $this->addSql('ALTER TABLE indicator_value DROP task_id');
        $this->addSql('ALTER TABLE task DROP attributes');
    }
}
