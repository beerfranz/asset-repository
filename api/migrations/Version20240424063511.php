<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240424063511 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE indicator DROP CONSTRAINT fk_d1349db343afa28a');
        $this->addSql('DROP INDEX idx_d1349db343afa28a');
        $this->addSql('ALTER TABLE indicator ADD task_template_identifier VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE indicator DROP task_template_id');
        $this->addSql('ALTER TABLE indicator_value DROP CONSTRAINT fk_d18506238db60186');
        $this->addSql('DROP INDEX idx_d18506238db60186');
        $this->addSql('ALTER TABLE indicator_value ADD task_identifier VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE indicator_value DROP task_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE indicator ADD task_template_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE indicator DROP task_template_identifier');
        $this->addSql('ALTER TABLE indicator ADD CONSTRAINT fk_d1349db343afa28a FOREIGN KEY (task_template_id) REFERENCES task_template (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_d1349db343afa28a ON indicator (task_template_id)');
        $this->addSql('ALTER TABLE indicator_value ADD task_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE indicator_value DROP task_identifier');
        $this->addSql('ALTER TABLE indicator_value ADD CONSTRAINT fk_d18506238db60186 FOREIGN KEY (task_id) REFERENCES task (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_d18506238db60186 ON indicator_value (task_id)');
    }
}
