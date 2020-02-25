<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200207085737 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1419C3385 FOREIGN KEY (pole_id) REFERENCES pole (id)');
        // $this->addSql('CREATE INDEX IDX_64C19C1419C3385 ON category (pole_id)');
        $this->addSql('ALTER TABLE pole ADD color LONGTEXT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1419C3385');
        $this->addSql('DROP INDEX IDX_64C19C1419C3385 ON category');
        $this->addSql('ALTER TABLE pole DROP color');
    }
}
