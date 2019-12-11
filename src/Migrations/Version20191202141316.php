<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191202141316 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        
        // $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64993B230BE');
        // $this->addSql('ALTER TABLE user DROP admin_groupe_id');
        // $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649A0905086 FOREIGN KEY (poste_id) REFERENCES poste (id)');
        // $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6497A45358C FOREIGN KEY (groupe_id) REFERENCES groupe (id)');
        // $this->addSql('CREATE INDEX IDX_8D93D649A0905086 ON user (poste_id)');
        // $this->addSql('CREATE INDEX IDX_8D93D6497A45358C ON user (groupe_id)');
        // $this->addSql('ALTER TABLE groupe ADD responsable_id INT NOT NULL');
        // $this->addSql('ALTER TABLE groupe ADD CONSTRAINT FK_4B98C2153C59D72 FOREIGN KEY (responsable_id) REFERENCES user (id)');
        // $this->addSql('CREATE INDEX IDX_4B98C2153C59D72 ON groupe (responsable_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE groupe DROP FOREIGN KEY FK_4B98C2153C59D72');
        $this->addSql('DROP INDEX IDX_4B98C2153C59D72 ON groupe');
        $this->addSql('ALTER TABLE groupe DROP responsable_id');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649A0905086');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6497A45358C');
        $this->addSql('DROP INDEX IDX_8D93D649A0905086 ON user');
        $this->addSql('DROP INDEX IDX_8D93D6497A45358C ON user');
        $this->addSql('ALTER TABLE user ADD admin_groupe_id INT DEFAULT NULL');
    }
}
