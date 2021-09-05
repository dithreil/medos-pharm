<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210905125017 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Add tables: characteristics, nomenclatures, prices, producers';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE characteristics (
            id CHAR(36) NOT NULL COMMENT '(DC2Type:guid)',
            nomenclature_id CHAR(36) DEFAULT NULL COMMENT '(DC2Type:guid)',
            serial VARCHAR(80) NOT NULL,
            expire_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            create_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            update_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            delete_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            INDEX IDX_7037B15690BFD4B8 (nomenclature_id),
            PRIMARY KEY(id))
        DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE nomenclatures (
            id CHAR(36) NOT NULL COMMENT '(DC2Type:guid)',
            producer_id CHAR(36) DEFAULT NULL COMMENT '(DC2Type:guid)',
            name VARCHAR(255) NOT NULL,
            price_type SMALLINT NOT NULL,
            medical_form SMALLINT NOT NULL,
            create_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            update_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            delete_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            INDEX IDX_9E1265E589B658FE (producer_id),
            PRIMARY KEY(id))
        DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE prices (
            id CHAR(36) NOT NULL COMMENT '(DC2Type:guid)',
            characteristic_id CHAR(36) DEFAULT NULL COMMENT '(DC2Type:guid)',
            value DOUBLE PRECISION NOT NULL,
            create_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            update_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            delete_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            INDEX IDX_E4CB6D59DEE9D12B (characteristic_id),
            PRIMARY KEY(id))
        DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE producers (
            id CHAR(36) NOT NULL COMMENT '(DC2Type:guid)',
            short_name VARCHAR(20) NOT NULL,
            full_name VARCHAR(255) NOT NULL,
            country VARCHAR(100) NOT NULL,
            create_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            update_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            PRIMARY KEY(id))
        DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql('ALTER TABLE characteristics ADD CONSTRAINT FK_7037B15690BFD4B8 FOREIGN KEY (nomenclature_id) REFERENCES nomenclatures (id)');
        $this->addSql('ALTER TABLE nomenclatures ADD CONSTRAINT FK_9E1265E589B658FE FOREIGN KEY (producer_id) REFERENCES producers (id)');
        $this->addSql('ALTER TABLE prices ADD CONSTRAINT FK_E4CB6D59DEE9D12B FOREIGN KEY (characteristic_id) REFERENCES characteristics (id)');
        $this->addSql('ALTER TABLE employees ADD login_attempts_counter SMALLINT NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE prices DROP FOREIGN KEY FK_E4CB6D59DEE9D12B');
        $this->addSql('ALTER TABLE characteristics DROP FOREIGN KEY FK_7037B15690BFD4B8');
        $this->addSql('ALTER TABLE nomenclatures DROP FOREIGN KEY FK_9E1265E589B658FE');
        $this->addSql('DROP TABLE characteristics');
        $this->addSql('DROP TABLE nomenclatures');
        $this->addSql('DROP TABLE prices');
        $this->addSql('DROP TABLE producers');
        $this->addSql('ALTER TABLE employees DROP login_attempts_counter');
    }
}
