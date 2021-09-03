<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210513113754 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Table [clients]: add login_attempts_counter';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql("ALTER TABLE clients ADD login_attempts_counter SMALLINT NOT NULL");
    }

    public function down(Schema $schema) : void
    {
        $this->addSql("ALTER TABLE clients DROP login_attempts_counter");
    }
}
