<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210927132323 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Add key setting key to null on deleted stock changes';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE price_changes DROP FOREIGN KEY FK_4B067061B9A16D1C');
        $this->addSql('ALTER TABLE price_changes ADD CONSTRAINT FK_4B067061B9A16D1C FOREIGN KEY (stock_change_id) REFERENCES stock_changes (id) ON UPDATE NO ACTION ON DELETE SET NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE price_changes DROP FOREIGN KEY FK_4B067061B9A16D1C');
        $this->addSql('ALTER TABLE price_changes ADD CONSTRAINT FK_4B067061B9A16D1C FOREIGN KEY (stock_change_id) REFERENCES stock_changes (id)');
    }
}
