<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191127105758 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE antenne (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD antenne_id INT NOT NULL');

        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649399E1D33 FOREIGN KEY (antenne_id) REFERENCES antenne (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649399E1D33 ON user (antenne_id)');
        
        $this->addSql('ALTER TABLE sub_category ADD author_id INT NOT NULL');
        $this->addSql('ALTER TABLE sub_category ADD CONSTRAINT FK_BCE3F798F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_BCE3F798F675F31B ON sub_category (author_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649399E1D33');
        $this->addSql('DROP TABLE antenne');
        $this->addSql('ALTER TABLE sub_category DROP FOREIGN KEY FK_BCE3F798F675F31B');
        $this->addSql('DROP INDEX IDX_BCE3F798F675F31B ON sub_category');
        $this->addSql('DROP INDEX IDX_8D93D649399E1D33 ON user');
        $this->addSql('ALTER TABLE user DROP antenne_id');
    }
}
