<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210916070338 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Added views: wares and characteristic_prices';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE incomes ADD is_set TINYINT(1) NOT NULL, ADD comment VARCHAR(255) DEFAULT NULL');
        $this->addSql("CREATE
        ALGORITHM = UNDEFINED
        DEFINER = `root`@`localhost`
        SQL SECURITY DEFINER
        VIEW `characteristic_prices` AS
        SELECT
            `prices`.`id` AS `id`,
            `characteristics`.`id` AS `characteristic`,
            `characteristics`.`serial` AS `serial`,
            `characteristics`.`expire_time` AS `expire_time`,
            `prices`.`value` AS `retail_price`,
            `stores`.`id` AS `store`,
            `stores`.`name` AS `store_name`
        FROM
            ((`prices`
            JOIN `characteristics` ON ((`prices`.`characteristic_id` = `characteristics`.`id`)))
            JOIN `stores` ON ((`prices`.`store_id` = `stores`.`id`)))");
        $this->addSql("CREATE
        ALGORITHM = UNDEFINED
        DEFINER = `root`@`localhost`
        SQL SECURITY DEFINER
        VIEW `wares` AS
        SELECT
            `nomenclatures`.`id` AS `nomenclature`,
            `nomenclatures`.`name` AS `name`,
            `nomenclatures`.`medical_form` AS `medical_form`,
            `characteristics`.`id` AS `characteristic`,
            `characteristics`.`serial` AS `serial`,
            `characteristics`.`expire_time` AS `expire_time`,
            (SELECT `characteristic_prices`.`retail_price` WHERE `stock_documents`.`store_id` = `characteristic_prices`.`store` AND `characteristics`.`id` = `characteristic_prices`.`characteristic`) AS `retail_price`,
            `stock_documents`.`store_id` AS `store`,
            SUM(`stock_changes`.`value`) AS `stock`
        FROM
            ((((`stock_changes`
            JOIN `stock_documents` ON ((`stock_changes`.`document_id` = `stock_documents`.`id`)))
            JOIN `characteristic_prices` ON ((`stock_changes`.`characteristic_id` = `characteristic_prices`.`characteristic`)))
            JOIN `characteristics` ON ((`stock_changes`.`characteristic_id` = `characteristics`.`id`)))
            JOIN `nomenclatures` ON ((`characteristics`.`nomenclature_id` = `nomenclatures`.`id`)))
	    WHERE `stock_changes`.`is_set` = 1
        GROUP BY `characteristics`.`id` , `stock_documents`.`store_id`
        HAVING (`stock` > 0)");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE incomes DROP is_set, DROP comment');
        $this->addSql('DROP VIEW wares');
        $this->addSql('DROP VIEW characteristic_prices');
    }
}
