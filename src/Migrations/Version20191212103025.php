<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191212103025 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sheet ADD origin_id INT DEFAULT NULL, ADD status VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE sheet ADD CONSTRAINT FK_873C91E256A273CC FOREIGN KEY (origin_id) REFERENCES sheet (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_873C91E256A273CC ON sheet (origin_id)');
        // $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649A0905086 FOREIGN KEY (poste_id) REFERENCES poste (id)');
        // $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6497A45358C FOREIGN KEY (groupe_id) REFERENCES groupe (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sheet DROP FOREIGN KEY FK_873C91E256A273CC');
        $this->addSql('DROP INDEX UNIQ_873C91E256A273CC ON sheet');
        $this->addSql('ALTER TABLE sheet DROP origin_id, DROP status');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649A0905086');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6497A45358C');
        $this->addSql('ALTER TABLE user ADD admin_groupe_id INT DEFAULT NULL');
    }
}
