<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191124224327 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create default Cash account';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql(
            'INSERT INTO account (name, type_id, allocation_type, allocation_percent) VALUES' .
            '("Cash", (SELECT id FROM account_type WHERE name = "cash"), "value", 0.00)'
        );
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('TRUNCATE account');
    }
}
