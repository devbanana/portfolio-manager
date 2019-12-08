<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191208025045 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create slug field for portfolio';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE portfolio ADD slug VARCHAR(255)');
        $this->addSql("UPDATE portfolio SET slug = 'Cash-Reserve' WHERE name = 'Cash Reserve'");
        $this->addSql("UPDATE portfolio SET slug = 'Stocks' WHERE name = 'Stocks'");
        $this->addSql('ALTER TABLE portfolio CHANGE slug slug VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A9ED1062989D9B62 ON portfolio (slug)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_A9ED1062989D9B62 ON portfolio');
        $this->addSql('ALTER TABLE portfolio DROP slug');
    }
}
