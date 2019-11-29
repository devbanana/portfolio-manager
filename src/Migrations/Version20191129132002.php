<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191129132002 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Singularize asset class names';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("UPDATE asset_class SET name = 'Equity', slug = 'Equity' WHERE name = 'Equities'");
        $this->addSql("UPDATE asset_class SET name = 'Commodity', slug = 'Commodity' WHERE name = 'Commodities'");
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("UPDATE asset_class SET name = 'Equities', slug = 'Equities' WHERE name = 'Equity'");
        $this->addSql("UPDATE asset_class SET name = 'Commodities', slug = 'Commodities' WHERE name = 'Commodity'");
    }
}
