<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201124095042 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE antenne ADD monday_am_open TIME DEFAULT NULL, ADD monday_am_close TIME DEFAULT NULL, ADD monday_pm_open TIME DEFAULT NULL, ADD monday_pm_close TIME DEFAULT NULL, ADD tuesday_am_open TIME DEFAULT NULL, ADD tuesday_am_close TIME DEFAULT NULL, ADD tuesday_pm_open TIME DEFAULT NULL, ADD tuesday_pm_close TIME DEFAULT NULL, ADD wednesday_am_open TIME DEFAULT NULL, ADD wednesday_am_close TIME DEFAULT NULL, ADD wednesday_pm_open TIME DEFAULT NULL, ADD wednesday_pm_close TIME DEFAULT NULL, ADD thursday_am_open TIME DEFAULT NULL, ADD thursday_am_close TIME DEFAULT NULL, ADD thursday_pm_open TIME DEFAULT NULL, ADD thursday_pm_close TIME DEFAULT NULL, ADD friday_am_open TIME DEFAULT NULL, ADD friday_am_close TIME DEFAULT NULL, ADD friday_pm_open TIME DEFAULT NULL, ADD saturday_am_open TIME DEFAULT NULL, ADD saturday_am_close TIME DEFAULT NULL, ADD saturday_pm_open TIME DEFAULT NULL, ADD saturday_pm_close TIME DEFAULT NULL, ADD sunday_am_open TIME DEFAULT NULL, ADD sunday_am_close TIME DEFAULT NULL, ADD sunday_pm_open TIME DEFAULT NULL, ADD sunday_pm_close TIME DEFAULT NULL, CHANGE hours hours VARCHAR(255) DEFAULT NULL');
        // $this->addSql('ALTER TABLE sub_category RENAME INDEX uniq_bce3f79812469de2 TO IDX_BCE3F79812469DE2');
        $this->addSql('ALTER TABLE user CHANGE introduction introduction VARCHAR(255) DEFAULT NULL, CHANGE phone_number phone_number VARCHAR(255) DEFAULT NULL, CHANGE token token VARCHAR(255) DEFAULT NULL, CHANGE password_requested_at password_requested_at DATETIME DEFAULT NULL, CHANGE picture_filename picture_filename VARCHAR(255) DEFAULT NULL, CHANGE direct_number direct_number VARCHAR(255) DEFAULT NULL, CHANGE pro_number pro_number VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE document CHANGE sub_category_id sub_category_id INT DEFAULT NULL, CHANGE front_author_id front_author_id INT DEFAULT NULL, CHANGE status status VARCHAR(255) DEFAULT NULL, CHANGE front front TINYINT(1) DEFAULT NULL, CHANGE published_at published_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE sheet CHANGE sub_category_id sub_category_id INT DEFAULT NULL, CHANGE origin_id origin_id INT DEFAULT NULL, CHANGE organization_id organization_id INT DEFAULT NULL, CHANGE front_author_id front_author_id INT DEFAULT NULL, CHANGE title title VARCHAR(255) DEFAULT NULL, CHANGE status status VARCHAR(255) DEFAULT NULL, CHANGE published_at published_at DATETIME DEFAULT NULL, CHANGE subtitle subtitle VARCHAR(255) DEFAULT NULL, CHANGE picture_filename picture_filename VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE organization CHANGE last_author_id last_author_id INT DEFAULT NULL, CHANGE address address VARCHAR(255) DEFAULT NULL, CHANGE city city VARCHAR(255) DEFAULT NULL, CHANGE postcode postcode INT DEFAULT NULL, CHANGE phone_number phone_number VARCHAR(255) DEFAULT NULL, CHANGE email email VARCHAR(255) DEFAULT NULL, CHANGE website website VARCHAR(255) DEFAULT NULL, CHANGE logo_filename logo_filename VARCHAR(255) DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE interlocutor CHANGE last_author_id last_author_id INT DEFAULT NULL, CHANGE phone_number phone_number VARCHAR(255) DEFAULT NULL, CHANGE email email VARCHAR(255) DEFAULT NULL, CHANGE pro_number pro_number VARCHAR(255) DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE attachment CHANGE title title VARCHAR(255) DEFAULT NULL, CHANGE file file VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE header CHANGE title title VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE paragraph CHANGE title title VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE section CHANGE title title VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE antenne DROP monday_am_open, DROP monday_am_close, DROP monday_pm_open, DROP monday_pm_close, DROP tuesday_am_open, DROP tuesday_am_close, DROP tuesday_pm_open, DROP tuesday_pm_close, DROP wednesday_am_open, DROP wednesday_am_close, DROP wednesday_pm_open, DROP wednesday_pm_close, DROP thursday_am_open, DROP thursday_am_close, DROP thursday_pm_open, DROP thursday_pm_close, DROP friday_am_open, DROP friday_am_close, DROP friday_pm_open, DROP saturday_am_open, DROP saturday_am_close, DROP saturday_pm_open, DROP saturday_pm_close, DROP sunday_am_open, DROP sunday_am_close, DROP sunday_pm_open, DROP sunday_pm_close, CHANGE hours hours VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE attachment CHANGE title title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE file file VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE document CHANGE sub_category_id sub_category_id INT DEFAULT NULL, CHANGE front_author_id front_author_id INT DEFAULT NULL, CHANGE status status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE front front TINYINT(1) DEFAULT \'NULL\', CHANGE published_at published_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE header CHANGE title title VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE interlocutor CHANGE last_author_id last_author_id INT DEFAULT NULL, CHANGE phone_number phone_number VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE email email VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE pro_number pro_number VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE organization CHANGE last_author_id last_author_id INT DEFAULT NULL, CHANGE address address VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE city city VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE postcode postcode INT DEFAULT NULL, CHANGE phone_number phone_number VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE email email VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE website website VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE logo_filename logo_filename VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE paragraph CHANGE title title VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE section CHANGE title title VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE sheet CHANGE sub_category_id sub_category_id INT DEFAULT NULL, CHANGE origin_id origin_id INT DEFAULT NULL, CHANGE organization_id organization_id INT DEFAULT NULL, CHANGE front_author_id front_author_id INT DEFAULT NULL, CHANGE title title VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE status status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE published_at published_at DATETIME DEFAULT \'NULL\', CHANGE subtitle subtitle VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE picture_filename picture_filename VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE sub_category RENAME INDEX idx_bce3f79812469de2 TO UNIQ_BCE3F79812469DE2');
        $this->addSql('ALTER TABLE user CHANGE introduction introduction VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE phone_number phone_number VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE token token VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE password_requested_at password_requested_at DATETIME DEFAULT \'NULL\', CHANGE picture_filename picture_filename VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE direct_number direct_number VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE pro_number pro_number VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
    }
}
