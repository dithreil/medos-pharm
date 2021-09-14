<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210914193307 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Added tables: incomes, stock_changes, stock_documents, price_changes, price_documents';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE incomes (
            id CHAR(36) NOT NULL COMMENT '(DC2Type:guid)',
            supplier_id CHAR(36) DEFAULT NULL COMMENT '(DC2Type:guid)',
            store_id CHAR(36) DEFAULT NULL COMMENT '(DC2Type:guid)',
            stock_document_id CHAR(36) NOT NULL COMMENT '(DC2Type:guid)',
            price_document_id CHAR(36) NOT NULL COMMENT '(DC2Type:guid)',
            date DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            create_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            update_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            INDEX IDX_9DE2B5BD2ADD6D8C (supplier_id),
            INDEX IDX_9DE2B5BDB092A811 (store_id),
            UNIQUE INDEX UNIQ_9DE2B5BD1CF1DE03 (stock_document_id),
            UNIQUE INDEX UNIQ_9DE2B5BDC4E63421 (price_document_id),
        PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE price_changes (
            id CHAR(36) NOT NULL COMMENT '(DC2Type:guid)',
            characteristic_id CHAR(36) DEFAULT NULL COMMENT '(DC2Type:guid)',
            document_id CHAR(36) DEFAULT NULL COMMENT '(DC2Type:guid)',
            old_value DOUBLE PRECISION NOT NULL,
            new_value DOUBLE PRECISION NOT NULL,
            is_set TINYINT(1) NOT NULL,
            create_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            update_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            INDEX IDX_4B067061DEE9D12B (characteristic_id),
            INDEX IDX_4B067061C33F7837 (document_id),
        PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE price_documents (
            id CHAR(36) NOT NULL COMMENT '(DC2Type:guid)',
            store_id CHAR(36) DEFAULT NULL COMMENT '(DC2Type:guid)',
            income_id CHAR(36) DEFAULT NULL COMMENT '(DC2Type:guid)',
            is_set TINYINT(1) NOT NULL,
            type VARCHAR(30) NOT NULL,
            create_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            update_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            INDEX IDX_21014174B092A811 (store_id),
            UNIQUE INDEX UNIQ_21014174640ED2C0 (income_id),
        PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE stock_changes (
            id CHAR(36) NOT NULL COMMENT '(DC2Type:guid)',
            characteristic_id CHAR(36) DEFAULT NULL COMMENT '(DC2Type:guid)',
            document_id CHAR(36) DEFAULT NULL COMMENT '(DC2Type:guid)',
            value DOUBLE PRECISION NOT NULL,
            is_set TINYINT(1) NOT NULL,
            create_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            update_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            INDEX IDX_3986A89EDEE9D12B (characteristic_id),
            INDEX IDX_3986A89EC33F7837 (document_id),
        PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE stock_documents (
            id CHAR(36) NOT NULL COMMENT '(DC2Type:guid)',
            store_id CHAR(36) DEFAULT NULL COMMENT '(DC2Type:guid)',
            income_id CHAR(36) DEFAULT NULL COMMENT '(DC2Type:guid)',
            is_set TINYINT(1) NOT NULL,
            type VARCHAR(30) NOT NULL,
            create_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            update_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            INDEX IDX_3A2D9460B092A811 (store_id),
            UNIQUE INDEX UNIQ_3A2D9460640ED2C0 (income_id),
        PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql('ALTER TABLE incomes ADD CONSTRAINT FK_9DE2B5BD2ADD6D8C FOREIGN KEY (supplier_id) REFERENCES suppliers (id)');
        $this->addSql('ALTER TABLE incomes ADD CONSTRAINT FK_9DE2B5BDB092A811 FOREIGN KEY (store_id) REFERENCES stores (id)');
        $this->addSql('ALTER TABLE incomes ADD CONSTRAINT FK_9DE2B5BD1CF1DE03 FOREIGN KEY (stock_document_id) REFERENCES stock_documents (id)');
        $this->addSql('ALTER TABLE incomes ADD CONSTRAINT FK_9DE2B5BDC4E63421 FOREIGN KEY (price_document_id) REFERENCES price_documents (id)');
        $this->addSql('ALTER TABLE price_changes ADD CONSTRAINT FK_4B067061DEE9D12B FOREIGN KEY (characteristic_id) REFERENCES characteristics (id)');
        $this->addSql('ALTER TABLE price_changes ADD CONSTRAINT FK_4B067061C33F7837 FOREIGN KEY (document_id) REFERENCES price_documents (id)');
        $this->addSql('ALTER TABLE price_documents ADD CONSTRAINT FK_21014174B092A811 FOREIGN KEY (store_id) REFERENCES stores (id)');
        $this->addSql('ALTER TABLE price_documents ADD CONSTRAINT FK_21014174640ED2C0 FOREIGN KEY (income_id) REFERENCES incomes (id)');
        $this->addSql('ALTER TABLE stock_changes ADD CONSTRAINT FK_3986A89EDEE9D12B FOREIGN KEY (characteristic_id) REFERENCES characteristics (id)');
        $this->addSql('ALTER TABLE stock_changes ADD CONSTRAINT FK_3986A89EC33F7837 FOREIGN KEY (document_id) REFERENCES stock_documents (id)');
        $this->addSql('ALTER TABLE stock_documents ADD CONSTRAINT FK_3A2D9460B092A811 FOREIGN KEY (store_id) REFERENCES stores (id)');
        $this->addSql('ALTER TABLE stock_documents ADD CONSTRAINT FK_3A2D9460640ED2C0 FOREIGN KEY (income_id) REFERENCES incomes (id)');
        $this->addSql('ALTER TABLE characteristics ADD butch INT NOT NULL');
        $this->addSql("ALTER TABLE prices CHANGE delete_time delete_time DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'");
        $this->addSql("ALTER TABLE prices ADD store_id CHAR(36) DEFAULT NULL COMMENT '(DC2Type:guid)'");
        $this->addSql('ALTER TABLE prices ADD CONSTRAINT FK_E4CB6D59B092A811 FOREIGN KEY (store_id) REFERENCES stores (id)');
        $this->addSql('CREATE INDEX IDX_E4CB6D59B092A811 ON prices (store_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE price_documents DROP FOREIGN KEY FK_21014174640ED2C0');
        $this->addSql('ALTER TABLE stock_documents DROP FOREIGN KEY FK_3A2D9460640ED2C0');
        $this->addSql('ALTER TABLE incomes DROP FOREIGN KEY FK_9DE2B5BDC4E63421');
        $this->addSql('ALTER TABLE price_changes DROP FOREIGN KEY FK_4B067061C33F7837');
        $this->addSql('ALTER TABLE incomes DROP FOREIGN KEY FK_9DE2B5BD1CF1DE03');
        $this->addSql('ALTER TABLE stock_changes DROP FOREIGN KEY FK_3986A89EC33F7837');
        $this->addSql('DROP TABLE incomes');
        $this->addSql('DROP TABLE price_changes');
        $this->addSql('DROP TABLE price_documents');
        $this->addSql('DROP TABLE stock_changes');
        $this->addSql('DROP TABLE stock_documents');
        $this->addSql('ALTER TABLE characteristics DROP butch');
        $this->addSql('ALTER TABLE prices DROP FOREIGN KEY FK_E4CB6D59B092A811');
        $this->addSql('DROP INDEX IDX_E4CB6D59B092A811 ON prices');
        $this->addSql('ALTER TABLE prices DROP store_id');
        $this->addSql("ALTER TABLE prices CHANGE delete_time delete_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)'");
    }
}
