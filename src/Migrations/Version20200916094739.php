<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200916094739 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // $this->addSql('ALTER TABLE sub_category RENAME INDEX uniq_bce3f79812469de2 TO IDX_BCE3F79812469DE2');
        $this->addSql('ALTER TABLE interlocutor ADD last_author_id INT DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE interlocutor ADD CONSTRAINT FK_F3FE3BF5E1BA2C1D FOREIGN KEY (last_author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_F3FE3BF5E1BA2C1D ON interlocutor (last_author_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE interlocutor DROP FOREIGN KEY FK_F3FE3BF5E1BA2C1D');
        $this->addSql('DROP INDEX IDX_F3FE3BF5E1BA2C1D ON interlocutor');
        $this->addSql('ALTER TABLE interlocutor DROP last_author_id, DROP updated_at');
        $this->addSql('ALTER TABLE sub_category RENAME INDEX idx_bce3f79812469de2 TO UNIQ_BCE3F79812469DE2');
    }
}
