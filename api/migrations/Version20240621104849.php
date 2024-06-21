<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240621104849 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "authorization" (id INT NOT NULL, user_id INT DEFAULT NULL, namespace VARCHAR(255) NOT NULL, object VARCHAR(255) NOT NULL, relation VARCHAR(255) NOT NULL, context VARCHAR(255) DEFAULT NULL, refresh_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7A6D8BEFA76ED395 ON "authorization" (user_id)');
        $this->addSql('CREATE INDEX authorization_namespace_idx ON "authorization" (namespace)');
        $this->addSql('CREATE INDEX authorization_object_idx ON "authorization" (object)');
        $this->addSql('CREATE TABLE authorization_policy (id INT NOT NULL, identifier VARCHAR(255) NOT NULL, namespace VARCHAR(255) NOT NULL, object VARCHAR(255) NOT NULL, relation VARCHAR(255) NOT NULL, context VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EDF12374772E836A ON authorization_policy (identifier)');
        $this->addSql('CREATE INDEX authorization_policy_namespace_idx ON authorization_policy (namespace)');
        $this->addSql('CREATE INDEX authorization_policy_object_idx ON authorization_policy (object)');
        $this->addSql('CREATE TABLE authorization_policy_user_group (authorization_policy_id INT NOT NULL, user_group_id INT NOT NULL, PRIMARY KEY(authorization_policy_id, user_group_id))');
        $this->addSql('CREATE INDEX IDX_9F80312E41AAB0BA ON authorization_policy_user_group (authorization_policy_id)');
        $this->addSql('CREATE INDEX IDX_9F80312E1ED93D47 ON authorization_policy_user_group (user_group_id)');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(180) NOT NULL, subject VARCHAR(180) NOT NULL, roles JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649FBCE3E7A ON "user" (subject)');
        $this->addSql('CREATE TABLE user_user_group (user_id INT NOT NULL, user_group_id INT NOT NULL, PRIMARY KEY(user_id, user_group_id))');
        $this->addSql('CREATE INDEX IDX_28657971A76ED395 ON user_user_group (user_id)');
        $this->addSql('CREATE INDEX IDX_286579711ED93D47 ON user_user_group (user_group_id)');
        $this->addSql('CREATE TABLE user_group (id INT NOT NULL, identifier VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8F02BF9D772E836A ON user_group (identifier)');
        $this->addSql('ALTER TABLE "authorization" ADD CONSTRAINT FK_7A6D8BEFA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE authorization_policy_user_group ADD CONSTRAINT FK_9F80312E41AAB0BA FOREIGN KEY (authorization_policy_id) REFERENCES authorization_policy (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE authorization_policy_user_group ADD CONSTRAINT FK_9F80312E1ED93D47 FOREIGN KEY (user_group_id) REFERENCES user_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_user_group ADD CONSTRAINT FK_28657971A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_user_group ADD CONSTRAINT FK_286579711ED93D47 FOREIGN KEY (user_group_id) REFERENCES user_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "authorization" DROP CONSTRAINT FK_7A6D8BEFA76ED395');
        $this->addSql('ALTER TABLE authorization_policy_user_group DROP CONSTRAINT FK_9F80312E41AAB0BA');
        $this->addSql('ALTER TABLE authorization_policy_user_group DROP CONSTRAINT FK_9F80312E1ED93D47');
        $this->addSql('ALTER TABLE user_user_group DROP CONSTRAINT FK_28657971A76ED395');
        $this->addSql('ALTER TABLE user_user_group DROP CONSTRAINT FK_286579711ED93D47');
        $this->addSql('DROP TABLE "authorization"');
        $this->addSql('DROP TABLE authorization_policy');
        $this->addSql('DROP TABLE authorization_policy_user_group');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE user_user_group');
        $this->addSql('DROP TABLE user_group');
    }
}
