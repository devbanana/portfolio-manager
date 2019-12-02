<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191129082723 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Set slugs on asset classes';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("UPDATE asset_class SET slug = 'Cash' WHERE name = 'Cash'");
        $this->addSql("UPDATE asset_class SET slug = 'Equities' WHERE name = 'Equities'");
        $this->addSql("UPDATE asset_class SET slug = 'Fixed-Income' WHERE name = 'Fixed Income'");
        $this->addSql("UPDATE asset_class SET slug = 'Crypto' WHERE name = 'Crypto'");
        $this->addSql("UPDATE asset_class SET slug = 'Real-Estate' WHERE name = 'Real Estate'");
        $this->addSql("UPDATE asset_class SET slug = 'Commodities' WHERE name = 'Commodities'");
        $this->addSql("UPDATE asset_class SET slug = 'Other' WHERE name = 'Other'");
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE asset_class SET slug = NULL');
    }
}
