<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230306090747 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO asset_type (id, name) VALUES (nextval('asset_type_id_seq'), 'container'), (nextval('asset_type_id_seq'), 'helm'), (nextval('asset_type_id_seq'), 'pod'), (nextval('asset_type_id_seq'), 'cloud provider'), (nextval('asset_type_id_seq'), 'virtual machine')");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
    }
}
