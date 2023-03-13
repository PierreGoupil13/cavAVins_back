<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230208111759 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cave_bouteille (cave_id INT NOT NULL, bouteille_id INT NOT NULL, PRIMARY KEY(cave_id, bouteille_id))');
        $this->addSql('CREATE INDEX IDX_A7957BA77F05B85 ON cave_bouteille (cave_id)');
        $this->addSql('CREATE INDEX IDX_A7957BA7F1966394 ON cave_bouteille (bouteille_id)');
        $this->addSql('ALTER TABLE cave_bouteille ADD CONSTRAINT FK_A7957BA77F05B85 FOREIGN KEY (cave_id) REFERENCES cave (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cave_bouteille ADD CONSTRAINT FK_A7957BA7F1966394 FOREIGN KEY (bouteille_id) REFERENCES bouteille (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE cave_bouteille DROP CONSTRAINT FK_A7957BA77F05B85');
        $this->addSql('ALTER TABLE cave_bouteille DROP CONSTRAINT FK_A7957BA7F1966394');
        $this->addSql('DROP TABLE cave_bouteille');
    }
}
