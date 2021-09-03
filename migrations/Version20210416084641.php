<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210416084641 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE orders (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', client_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', employee_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', create_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', update_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', start_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', payment_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', cost DOUBLE PRECISION NOT NULL, INDEX IDX_E52FFDEE19EB6921 (client_id), INDEX IDX_E52FFDEE8C03F15C (employee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payments (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', order_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', amount DOUBLE PRECISION NOT NULL, status VARCHAR(255) NOT NULL, employee_name VARCHAR(255) NOT NULL, client_name VARCHAR(255) NOT NULL, create_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', update_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_65D29B328D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE19EB6921 FOREIGN KEY (client_id) REFERENCES clients (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE8C03F15C FOREIGN KEY (employee_id) REFERENCES employees (id)');
        $this->addSql('ALTER TABLE payments ADD CONSTRAINT FK_65D29B328D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE payments DROP FOREIGN KEY FK_65D29B328D9F6D38');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE8C03F15C');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE19EB6921');
        $this->addSql('DROP TABLE payments');
        $this->addSql('DROP TABLE orders');
    }
}
