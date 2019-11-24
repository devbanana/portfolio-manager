<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191124173639 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create default account types';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql(
            'INSERT INTO account_type (name) VALUES' .
            '("Cash"),' .
            '("Taxable"),' .
            '("Crypto"),' .
            '("IRA"),' .
            '("401(k)")'
        );
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('truncate account_type');
    }
}
