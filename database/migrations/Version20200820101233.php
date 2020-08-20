<?php

namespace Database\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20200820101233 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE mail_chimp_members (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', list_id VARCHAR(255) NOT NULL, mail_chimp_id VARCHAR(255) DEFAULT NULL, email_address VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, email_type VARCHAR(255) DEFAULT NULL, language VARCHAR(255) DEFAULT NULL, vip TINYINT(1) DEFAULT NULL, location LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', marketing_permissions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', ip_signup VARCHAR(255) DEFAULT NULL, timestamp_signup VARCHAR(255) DEFAULT NULL, ip_opt VARCHAR(255) DEFAULT NULL, timestamp_opt VARCHAR(255) DEFAULT NULL, tags LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', email_id VARCHAR(255) DEFAULT NULL, unique_email_id VARCHAR(255) DEFAULT NULL, member_rating INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE mail_chimp_members');
    }
}
