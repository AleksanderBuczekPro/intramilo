<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200225104133 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE4F34D596');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C4F34D596');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F4F34D596');
        $this->addSql('DROP TABLE ad');
        $this->addSql('DROP TABLE booking');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE image');
        // $this->addSql('ALTER TABLE sub_category CHANGE author_id author_id INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ad (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, title VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, slug VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, price DOUBLE PRECISION NOT NULL, introduction LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci, content LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci, cover_image VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, rooms INT NOT NULL, INDEX IDX_77E0ED58F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE booking (id INT AUTO_INCREMENT NOT NULL, booker_id INT NOT NULL, ad_id INT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, created_at DATETIME NOT NULL, amount DOUBLE PRECISION NOT NULL, comment LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, INDEX IDX_E00CEDDE4F34D596 (ad_id), INDEX IDX_E00CEDDE8B7E4006 (booker_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, ad_id INT NOT NULL, author_id INT NOT NULL, created_at DATETIME NOT NULL, rating INT NOT NULL, content LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci, INDEX IDX_9474526CF675F31B (author_id), INDEX IDX_9474526C4F34D596 (ad_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, ad_id INT NOT NULL, url VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, caption VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, INDEX IDX_C53D045F4F34D596 (ad_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE ad ADD CONSTRAINT FK_77E0ED58F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE4F34D596 FOREIGN KEY (ad_id) REFERENCES ad (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE8B7E4006 FOREIGN KEY (booker_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C4F34D596 FOREIGN KEY (ad_id) REFERENCES ad (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F4F34D596 FOREIGN KEY (ad_id) REFERENCES ad (id)');
        $this->addSql('ALTER TABLE sub_category CHANGE author_id author_id INT DEFAULT NULL');
    }
}
