<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240525235637 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE task_tag_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE task_task_tag (task_id INT NOT NULL, task_tag_id INT NOT NULL, PRIMARY KEY(task_id, task_tag_id))');
        $this->addSql('CREATE INDEX IDX_2CCBA5488DB60186 ON task_task_tag (task_id)');
        $this->addSql('CREATE INDEX IDX_2CCBA548817BE7C2 ON task_task_tag (task_tag_id)');
        $this->addSql('CREATE TABLE task_tag (id INT NOT NULL, name VARCHAR(255) NOT NULL, value VARCHAR(255) DEFAULT NULL, color VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE task_template_task_tag (task_template_id INT NOT NULL, task_tag_id INT NOT NULL, PRIMARY KEY(task_template_id, task_tag_id))');
        $this->addSql('CREATE INDEX IDX_DAAE232A43AFA28A ON task_template_task_tag (task_template_id)');
        $this->addSql('CREATE INDEX IDX_DAAE232A817BE7C2 ON task_template_task_tag (task_tag_id)');
        $this->addSql('ALTER TABLE task_task_tag ADD CONSTRAINT FK_2CCBA5488DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_task_tag ADD CONSTRAINT FK_2CCBA548817BE7C2 FOREIGN KEY (task_tag_id) REFERENCES task_tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_template_task_tag ADD CONSTRAINT FK_DAAE232A43AFA28A FOREIGN KEY (task_template_id) REFERENCES task_template (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_template_task_tag ADD CONSTRAINT FK_DAAE232A817BE7C2 FOREIGN KEY (task_tag_id) REFERENCES task_tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE task_tag_id_seq CASCADE');
        $this->addSql('ALTER TABLE task_task_tag DROP CONSTRAINT FK_2CCBA5488DB60186');
        $this->addSql('ALTER TABLE task_task_tag DROP CONSTRAINT FK_2CCBA548817BE7C2');
        $this->addSql('ALTER TABLE task_template_task_tag DROP CONSTRAINT FK_DAAE232A43AFA28A');
        $this->addSql('ALTER TABLE task_template_task_tag DROP CONSTRAINT FK_DAAE232A817BE7C2');
        $this->addSql('DROP TABLE task_task_tag');
        $this->addSql('DROP TABLE task_tag');
        $this->addSql('DROP TABLE task_template_task_tag');
    }
}
