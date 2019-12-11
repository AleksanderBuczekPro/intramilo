<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191119133512 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE tools (sheet_id INT NOT NULL, sheet_tool_id INT NOT NULL, INDEX IDX_EAFADE778B1206A5 (sheet_id), INDEX IDX_EAFADE773073771 (sheet_tool_id), PRIMARY KEY(sheet_id, sheet_tool_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tools ADD CONSTRAINT FK_EAFADE778B1206A5 FOREIGN KEY (sheet_id) REFERENCES sheet (id)');
        $this->addSql('ALTER TABLE tools ADD CONSTRAINT FK_EAFADE773073771 FOREIGN KEY (sheet_tool_id) REFERENCES sheet (id)');
        $this->addSql('ALTER TABLE sheet DROP FOREIGN KEY FK_873C91E28B1206A5');
        $this->addSql('DROP INDEX IDX_873C91E28B1206A5 ON sheet');
        $this->addSql('ALTER TABLE sheet DROP sheet_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE tools');
        $this->addSql('ALTER TABLE sheet ADD sheet_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sheet ADD CONSTRAINT FK_873C91E28B1206A5 FOREIGN KEY (sheet_id) REFERENCES sheet (id)');
        $this->addSql('CREATE INDEX IDX_873C91E28B1206A5 ON sheet (sheet_id)');
    }
}
