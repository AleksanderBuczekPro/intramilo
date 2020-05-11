<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200510062118 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE pole ADD picture_filename VARCHAR(255) NOT NULL');
        // $this->addSql('ALTER TABLE sub_category RENAME INDEX uniq_bce3f79812469de2 TO IDX_BCE3F79812469DE2');
        $this->addSql('ALTER TABLE user CHANGE groupe_id groupe_id INT NOT NULL, CHANGE phone_number phone_number VARCHAR(255) DEFAULT NULL, CHANGE token token VARCHAR(255) DEFAULT NULL, CHANGE password_requested_at password_requested_at DATETIME DEFAULT NULL, CHANGE picture_filename picture_filename VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE document CHANGE sub_category_id sub_category_id INT DEFAULT NULL, CHANGE front_author_id front_author_id INT DEFAULT NULL, CHANGE status status VARCHAR(255) DEFAULT NULL, CHANGE front front TINYINT(1) DEFAULT NULL, CHANGE published_at published_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE organization CHANGE phone_number phone_number VARCHAR(255) DEFAULT NULL, CHANGE email email VARCHAR(255) DEFAULT NULL, CHANGE website website VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE interlocutor CHANGE phone_number phone_number VARCHAR(255) DEFAULT NULL, CHANGE email email VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE sheet CHANGE sub_category_id sub_category_id INT DEFAULT NULL, CHANGE origin_id origin_id INT DEFAULT NULL, CHANGE front_author_id front_author_id INT DEFAULT NULL, CHANGE status status VARCHAR(255) DEFAULT NULL, CHANGE published_at published_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE document CHANGE sub_category_id sub_category_id INT DEFAULT NULL, CHANGE front_author_id front_author_id INT DEFAULT NULL, CHANGE status status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE front front TINYINT(1) DEFAULT \'NULL\', CHANGE published_at published_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE interlocutor CHANGE phone_number phone_number VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE email email VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE organization CHANGE phone_number phone_number VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE email email VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE website website VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE pole DROP picture_filename');
        $this->addSql('ALTER TABLE sheet CHANGE sub_category_id sub_category_id INT DEFAULT NULL, CHANGE origin_id origin_id INT DEFAULT NULL, CHANGE front_author_id front_author_id INT DEFAULT NULL, CHANGE status status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE published_at published_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE sub_category RENAME INDEX idx_bce3f79812469de2 TO UNIQ_BCE3F79812469DE2');
        $this->addSql('ALTER TABLE user CHANGE groupe_id groupe_id INT DEFAULT NULL, CHANGE phone_number phone_number VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE token token VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE password_requested_at password_requested_at DATETIME DEFAULT \'NULL\', CHANGE picture_filename picture_filename VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
    }
}
