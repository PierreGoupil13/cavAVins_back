<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230320150210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reset_token ADD bearer_id INT NOT NULL');
        $this->addSql('ALTER TABLE reset_token ADD CONSTRAINT FK_D7C8DC196E5874C0 FOREIGN KEY (bearer_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D7C8DC196E5874C0 ON reset_token (bearer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE reset_token DROP CONSTRAINT FK_D7C8DC196E5874C0');
        $this->addSql('DROP INDEX UNIQ_D7C8DC196E5874C0');
        $this->addSql('ALTER TABLE reset_token DROP bearer_id');
    }
}
