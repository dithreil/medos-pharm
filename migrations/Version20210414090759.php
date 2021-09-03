<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210414090759 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("CREATE TABLE clients (
            id CHAR(36) NOT NULL COMMENT '(DC2Type:guid)',
            snils VARCHAR(11) DEFAULT NULL,
            birth_date DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            skype VARCHAR(255) DEFAULT NULL,
            whatsapp VARCHAR(11) DEFAULT NULL,
            first_name VARCHAR(255) NOT NULL,
            last_name VARCHAR(255) NOT NULL,
            patronymic VARCHAR(255) DEFAULT NULL,
            roles JSON NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone_number VARCHAR(11) NOT NULL,
            is_active TINYINT(1) NOT NULL,
            salt VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            create_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            update_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE confirmation_tokens (
            id CHAR(36) NOT NULL COMMENT '(DC2Type:guid)',
            client_id CHAR(36) DEFAULT NULL COMMENT '(DC2Type:guid)',
            token_type VARCHAR(255) NOT NULL,
            token VARCHAR(255) NOT NULL,
            create_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            UNIQUE INDEX UNIQ_75A5965E19EB6921 (client_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE employees (
            id CHAR(36) NOT NULL COMMENT '(DC2Type:guid)',
            specialization VARCHAR(255) NOT NULL,
            first_name VARCHAR(255) NOT NULL,
            last_name VARCHAR(255) NOT NULL,
            patronymic VARCHAR(255) DEFAULT NULL,
            roles JSON NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone_number VARCHAR(11) NOT NULL,
            is_active TINYINT(1) NOT NULL,
            salt VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            create_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            update_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("ALTER TABLE confirmation_tokens ADD CONSTRAINT FK_75A5965EA76ED395 FOREIGN KEY (client_id) REFERENCES clients (id)");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("ALTER TABLE confirmation_tokens DROP FOREIGN KEY FK_75A5965EA76ED395");
        $this->addSql("DROP TABLE employees");
        $this->addSql("DROP TABLE confirmation_tokens");
        $this->addSql("DROP TABLE clients");
    }
}
