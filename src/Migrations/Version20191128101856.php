<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191128101856 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE groupe (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE poste (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        
        
        $this->addSql('ALTER TABLE user ADD poste_id INT NOT NULL, ADD groupe_id INT NOT NULL');

        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649A0905086 FOREIGN KEY (poste_id) REFERENCES poste (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6497A45358C FOREIGN KEY (groupe_id) REFERENCES groupe (id)');

        $this->addSql('CREATE INDEX IDX_8D93D649A0905086 ON user (poste_id)');
        $this->addSql('CREATE INDEX IDX_8D93D6497A45358C ON user (groupe_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64993B230BE');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6497A45358C');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649A0905086');
        $this->addSql('DROP TABLE groupe');
        $this->addSql('DROP TABLE poste');
        $this->addSql('DROP INDEX IDX_8D93D649A0905086 ON user');
        $this->addSql('DROP INDEX IDX_8D93D64993B230BE ON user');
        $this->addSql('DROP INDEX IDX_8D93D6497A45358C ON user');
        $this->addSql('ALTER TABLE user DROP poste_id, DROP admin_groupe_id, DROP groupe_id');
    }
}
