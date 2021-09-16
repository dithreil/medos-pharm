<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210916070328 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Add connection between stock change and price change';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE price_changes ADD stock_change_id CHAR(36) DEFAULT NULL COMMENT '(DC2Type:guid)'");
        $this->addSql("ALTER TABLE price_changes ADD CONSTRAINT FK_4B067061B9A16D1C FOREIGN KEY (stock_change_id) REFERENCES stock_changes (id)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_4B067061B9A16D1C ON price_changes (stock_change_id)");
        $this->addSql("ALTER TABLE stock_changes ADD price_change_id CHAR(36) NOT NULL COMMENT '(DC2Type:guid)'");
        $this->addSql("ALTER TABLE stock_changes ADD CONSTRAINT FK_3986A89EA28DB808 FOREIGN KEY (price_change_id) REFERENCES price_changes (id)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_3986A89EA28DB808 ON stock_changes (price_change_id)");
        $this->addSql('ALTER TABLE incomes ADD amount DOUBLE PRECISION DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE price_changes DROP FOREIGN KEY FK_4B067061B9A16D1C');
        $this->addSql('DROP INDEX UNIQ_4B067061B9A16D1C ON price_changes');
        $this->addSql('ALTER TABLE price_changes DROP stock_change_id');
        $this->addSql('ALTER TABLE stock_changes DROP FOREIGN KEY FK_3986A89EA28DB808');
        $this->addSql('DROP INDEX UNIQ_3986A89EA28DB808 ON stock_changes');
        $this->addSql('ALTER TABLE stock_changes DROP price_change_id');
        $this->addSql('ALTER TABLE incomes DROP amount');
    }
}
