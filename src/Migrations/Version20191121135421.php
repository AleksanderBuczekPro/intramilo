<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191121135421 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sheet_document (sheet_id INT NOT NULL, document_id INT NOT NULL, INDEX IDX_6D3539528B1206A5 (sheet_id), INDEX IDX_6D353952C33F7837 (document_id), PRIMARY KEY(sheet_id, document_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sheet_document ADD CONSTRAINT FK_6D3539528B1206A5 FOREIGN KEY (sheet_id) REFERENCES sheet (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sheet_document ADD CONSTRAINT FK_6D353952C33F7837 FOREIGN KEY (document_id) REFERENCES document (id) ON DELETE CASCADE');
        // $this->addSql('DROP TABLE sheetdocument');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sheetdocument (sheet_id INT NOT NULL, document_id INT NOT NULL, INDEX IDX_D92B1CE08B1206A5 (sheet_id), INDEX IDX_D92B1CE0C33F7837 (document_id), PRIMARY KEY(sheet_id, document_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE sheetdocument ADD CONSTRAINT FK_D92B1CE08B1206A5 FOREIGN KEY (sheet_id) REFERENCES sheet (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sheetdocument ADD CONSTRAINT FK_D92B1CE0C33F7837 FOREIGN KEY (document_id) REFERENCES document (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE sheet_document');
    }
}
