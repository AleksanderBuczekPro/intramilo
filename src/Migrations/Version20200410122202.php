<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200410122202 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sub_category_user (sub_category_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_C9A56BD0F7BFE87C (sub_category_id), INDEX IDX_C9A56BD0A76ED395 (user_id), PRIMARY KEY(sub_category_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sub_category_user ADD CONSTRAINT FK_C9A56BD0F7BFE87C FOREIGN KEY (sub_category_id) REFERENCES sub_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sub_category_user ADD CONSTRAINT FK_C9A56BD0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user CHANGE phone_number phone_number VARCHAR(255) DEFAULT NULL, CHANGE token token VARCHAR(255) DEFAULT NULL, CHANGE password_requested_at password_requested_at DATETIME DEFAULT NULL, CHANGE picture_filename picture_filename VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE document ADD author_id INT NOT NULL, CHANGE sub_category_id sub_category_id INT DEFAULT NULL, CHANGE status status VARCHAR(255) DEFAULT NULL, CHANGE front front TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_D8698A76F675F31B ON document (author_id)');
        $this->addSql('ALTER TABLE interlocutor CHANGE phone_number phone_number VARCHAR(255) DEFAULT NULL, CHANGE email email VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE organization CHANGE phone_number phone_number VARCHAR(255) DEFAULT NULL, CHANGE email email VARCHAR(255) DEFAULT NULL, CHANGE website website VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE sheet ADD author_id INT NOT NULL, CHANGE sub_category_id sub_category_id INT DEFAULT NULL, CHANGE origin_id origin_id INT DEFAULT NULL, CHANGE status status VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE sheet ADD CONSTRAINT FK_873C91E2F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_873C91E2F675F31B ON sheet (author_id)');
        $this->addSql('ALTER TABLE sub_category DROP FOREIGN KEY FK_BCE3F798F675F31B');
        // $this->addSql('DROP INDEX IDX_BCE3F798F675F31B ON sub_category');
        $this->addSql('ALTER TABLE sub_category DROP author_id');
        // $this->addSql('ALTER TABLE sub_category RENAME INDEX uniq_bce3f79812469de2 TO IDX_BCE3F79812469DE2');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sub_category_user');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A76F675F31B');
        $this->addSql('DROP INDEX IDX_D8698A76F675F31B ON document');
        $this->addSql('ALTER TABLE document DROP author_id, CHANGE sub_category_id sub_category_id INT DEFAULT NULL, CHANGE status status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE front front TINYINT(1) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE interlocutor CHANGE phone_number phone_number VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE email email VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE organization CHANGE phone_number phone_number VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE email email VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE website website VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE sheet DROP FOREIGN KEY FK_873C91E2F675F31B');
        $this->addSql('DROP INDEX IDX_873C91E2F675F31B ON sheet');
        $this->addSql('ALTER TABLE sheet DROP author_id, CHANGE sub_category_id sub_category_id INT DEFAULT NULL, CHANGE origin_id origin_id INT DEFAULT NULL, CHANGE status status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE sub_category ADD author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sub_category ADD CONSTRAINT FK_BCE3F798F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_BCE3F798F675F31B ON sub_category (author_id)');
        $this->addSql('ALTER TABLE sub_category RENAME INDEX idx_bce3f79812469de2 TO UNIQ_BCE3F79812469DE2');
        $this->addSql('ALTER TABLE user CHANGE phone_number phone_number VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE token token VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE password_requested_at password_requested_at DATETIME DEFAULT \'NULL\', CHANGE picture_filename picture_filename VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
    }
}
