<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200224121950 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE pole ADD label_color VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE category CHANGE pole_id pole_id INT NOT NULL');
        // $this->addSql('ALTER TABLE user CHANGE antenne_id antenne_id INT NOT NULL, CHANGE groupe_id groupe_id INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE category CHANGE pole_id pole_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pole DROP label_color');
        $this->addSql('ALTER TABLE user CHANGE antenne_id antenne_id INT DEFAULT NULL, CHANGE groupe_id groupe_id INT DEFAULT NULL');
    }
}
