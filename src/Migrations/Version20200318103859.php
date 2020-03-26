<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200318103859 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE interlocutor (id INT AUTO_INCREMENT NOT NULL, organization_id INT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, function VARCHAR(255) NOT NULL, phone_number VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, INDEX IDX_F3FE3BF532C8A3DE (organization_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organization (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, postcode INT NOT NULL, phone_number VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE interlocutor ADD CONSTRAINT FK_F3FE3BF532C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE document ADD organization_id INT NOT NULL, DROP organization, CHANGE sub_category_id sub_category_id INT DEFAULT NULL, CHANGE status status VARCHAR(255) DEFAULT NULL, CHANGE front front TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A7632C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('CREATE INDEX IDX_D8698A7632C8A3DE ON document (organization_id)');
        // $this->addSql('ALTER TABLE sub_category CHANGE author_id author_id INT NOT NULL');
        // $this->addSql('ALTER TABLE sub_category RENAME INDEX uniq_bce3f79812469de2 TO IDX_BCE3F79812469DE2');
        $this->addSql('ALTER TABLE groupe CHANGE responsable_id responsable_id INT NOT NULL');
        $this->addSql('ALTER TABLE sheet ADD organization_id INT NOT NULL, DROP organization, CHANGE sub_category_id sub_category_id INT DEFAULT NULL, CHANGE origin_id origin_id INT DEFAULT NULL, CHANGE status status VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE sheet ADD CONSTRAINT FK_873C91E232C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('CREATE INDEX IDX_873C91E232C8A3DE ON sheet (organization_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A7632C8A3DE');
        $this->addSql('ALTER TABLE sheet DROP FOREIGN KEY FK_873C91E232C8A3DE');
        $this->addSql('ALTER TABLE interlocutor DROP FOREIGN KEY FK_F3FE3BF532C8A3DE');
        $this->addSql('DROP TABLE interlocutor');
        $this->addSql('DROP TABLE organization');
        $this->addSql('DROP INDEX IDX_D8698A7632C8A3DE ON document');
        $this->addSql('ALTER TABLE document ADD organization VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP organization_id, CHANGE sub_category_id sub_category_id INT DEFAULT NULL, CHANGE status status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE front front TINYINT(1) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE groupe CHANGE responsable_id responsable_id INT DEFAULT NULL');
        $this->addSql('DROP INDEX IDX_873C91E232C8A3DE ON sheet');
        $this->addSql('ALTER TABLE sheet ADD organization VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, DROP organization_id, CHANGE sub_category_id sub_category_id INT DEFAULT NULL, CHANGE origin_id origin_id INT DEFAULT NULL, CHANGE status status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE sub_category CHANGE author_id author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sub_category RENAME INDEX idx_bce3f79812469de2 TO UNIQ_BCE3F79812469DE2');
        $this->addSql('ALTER TABLE user ADD picture VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, ADD description LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE phone_number phone_number VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE token token VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE password_requested_at password_requested_at DATETIME DEFAULT \'NULL\', CHANGE picture_filename picture_filename VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
    }
}
