<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191203132144 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add unallocated field';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE portfolio ADD unallocated TINYINT(1)');
        $this->addSql('UPDATE portfolio SET unallocated = TRUE WHERE allocation_percent IS NULL');
        $this->addSql('UPDATE portfolio SET unallocated = FALSE WHERE allocation_percent IS NOT NULL');
        $this->addSql('ALTER TABLE portfolio change unallocated unallocated TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE portfolio DROP unallocated');
    }
}
