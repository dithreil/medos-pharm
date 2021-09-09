<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210908192837 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Delete time becomes nullable for characteristics and nomenclatures';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE characteristics CHANGE delete_time delete_time DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'");
        $this->addSql("ALTER TABLE nomenclatures CHANGE delete_time delete_time DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->addSql("ALTER TABLE characteristics CHANGE delete_time delete_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)'");
        $this->addSql("ALTER TABLE nomenclatures CHANGE delete_time delete_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)'");
    }
}
