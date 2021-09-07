<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210907071322 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Added tables: stores and suppliers, added connection from employee to store';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE stores (
            id CHAR(36) NOT NULL COMMENT '(DC2Type:guid)',
            name VARCHAR(255) NOT NULL,
            address VARCHAR(255) NOT NULL,
            description VARCHAR(255) DEFAULT NULL,
            create_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            update_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
        PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE suppliers (
            id CHAR(36) NOT NULL COMMENT '(DC2Type:guid)',
            name VARCHAR(255) NOT NULL,
            address VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone_number VARCHAR(11) NOT NULL,
            information VARCHAR(255) NOT NULL,
            create_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            update_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
        PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("ALTER TABLE employees ADD store_id CHAR(36) DEFAULT NULL COMMENT '(DC2Type:guid)'");
        $this->addSql('ALTER TABLE employees ADD CONSTRAINT FK_BA82C300B092A811 FOREIGN KEY (store_id) REFERENCES stores (id)');
        $this->addSql('CREATE INDEX IDX_BA82C300B092A811 ON employees (store_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE employees DROP FOREIGN KEY FK_BA82C300B092A811');
        $this->addSql('DROP TABLE stores');
        $this->addSql('DROP TABLE suppliers');
        $this->addSql('DROP INDEX IDX_BA82C300B092A811 ON employees');
        $this->addSql('ALTER TABLE employees DROP store_id');
    }
}
