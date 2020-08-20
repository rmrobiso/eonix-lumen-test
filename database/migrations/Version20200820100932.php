<?php

namespace Database\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20200820100932 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE mail_chimp_lists (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, contact LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', permission_reminder VARCHAR(255) NOT NULL, campaign_defaults LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', email_type_option TINYINT(1) NOT NULL, use_archive_bar TINYINT(1) DEFAULT NULL, notify_on_subscribe VARCHAR(255) DEFAULT NULL, notify_on_unsubscribe VARCHAR(255) DEFAULT NULL, visibility VARCHAR(255) DEFAULT NULL, double_optin TINYINT(1) DEFAULT NULL, marketing_permissions TINYINT(1) DEFAULT NULL, mail_chimp_id VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE mail_chimp_lists');
    }
}
