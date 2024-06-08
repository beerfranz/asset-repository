<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240608095531 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE assessment_plan (id SERIAL NOT NULL, asset_id INT DEFAULT NULL, identifier VARCHAR(255) NOT NULL, title VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4F296671772E836A ON assessment_plan (identifier)');
        $this->addSql('CREATE INDEX IDX_4F2966715DA1941 ON assessment_plan (asset_id)');
        $this->addSql('CREATE TABLE assessment_plan_task (assessment_plan_id INT NOT NULL, task_id INT NOT NULL, PRIMARY KEY(assessment_plan_id, task_id))');
        $this->addSql('CREATE INDEX IDX_95D9E43B42679355 ON assessment_plan_task (assessment_plan_id)');
        $this->addSql('CREATE INDEX IDX_95D9E43B8DB60186 ON assessment_plan_task (task_id)');
        $this->addSql('CREATE TABLE assessment_sequence (id SERIAL NOT NULL, sequence_number INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE assessment_template (id SERIAL NOT NULL, identifier VARCHAR(255) NOT NULL, rules JSON DEFAULT NULL, title VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EB160EA5772E836A ON assessment_template (identifier)');
        $this->addSql('CREATE TABLE assessment_template_task_template (assessment_template_id INT NOT NULL, task_template_id INT NOT NULL, PRIMARY KEY(assessment_template_id, task_template_id))');
        $this->addSql('CREATE INDEX IDX_AF932CF9D3A5C28F ON assessment_template_task_template (assessment_template_id)');
        $this->addSql('CREATE INDEX IDX_AF932CF943AFA28A ON assessment_template_task_template (task_template_id)');
        $this->addSql('CREATE TABLE assessment_template_asset (assessment_template_id INT NOT NULL, asset_id INT NOT NULL, PRIMARY KEY(assessment_template_id, asset_id))');
        $this->addSql('CREATE INDEX IDX_AEB3BFFED3A5C28F ON assessment_template_asset (assessment_template_id)');
        $this->addSql('CREATE INDEX IDX_AEB3BFFE5DA1941 ON assessment_template_asset (asset_id)');
        $this->addSql('ALTER TABLE assessment_plan ADD CONSTRAINT FK_4F2966715DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE assessment_plan_task ADD CONSTRAINT FK_95D9E43B42679355 FOREIGN KEY (assessment_plan_id) REFERENCES assessment_plan (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE assessment_plan_task ADD CONSTRAINT FK_95D9E43B8DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE assessment_template_task_template ADD CONSTRAINT FK_AF932CF9D3A5C28F FOREIGN KEY (assessment_template_id) REFERENCES assessment_template (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE assessment_template_task_template ADD CONSTRAINT FK_AF932CF943AFA28A FOREIGN KEY (task_template_id) REFERENCES task_template (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE assessment_template_asset ADD CONSTRAINT FK_AEB3BFFED3A5C28F FOREIGN KEY (assessment_template_id) REFERENCES assessment_template (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE assessment_template_asset ADD CONSTRAINT FK_AEB3BFFE5DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE assessment_plan DROP CONSTRAINT FK_4F2966715DA1941');
        $this->addSql('ALTER TABLE assessment_plan_task DROP CONSTRAINT FK_95D9E43B42679355');
        $this->addSql('ALTER TABLE assessment_plan_task DROP CONSTRAINT FK_95D9E43B8DB60186');
        $this->addSql('ALTER TABLE assessment_template_task_template DROP CONSTRAINT FK_AF932CF9D3A5C28F');
        $this->addSql('ALTER TABLE assessment_template_task_template DROP CONSTRAINT FK_AF932CF943AFA28A');
        $this->addSql('ALTER TABLE assessment_template_asset DROP CONSTRAINT FK_AEB3BFFED3A5C28F');
        $this->addSql('ALTER TABLE assessment_template_asset DROP CONSTRAINT FK_AEB3BFFE5DA1941');
        $this->addSql('DROP TABLE assessment_plan');
        $this->addSql('DROP TABLE assessment_plan_task');
        $this->addSql('DROP TABLE assessment_sequence');
        $this->addSql('DROP TABLE assessment_template');
        $this->addSql('DROP TABLE assessment_template_task_template');
        $this->addSql('DROP TABLE assessment_template_asset');
    }
}
