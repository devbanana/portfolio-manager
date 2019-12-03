<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191203042913 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create portfolio and portfolio_holding';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE portfolio_holding (id INT AUTO_INCREMENT NOT NULL, asset_id INT NOT NULL, portfolio_id INT NOT NULL, shares NUMERIC(15, 8) NOT NULL, total_value NUMERIC(10, 2) NOT NULL, invested NUMERIC(10, 2) NOT NULL, INDEX IDX_D0AF7D85DA1941 (asset_id), INDEX IDX_D0AF7D8B96B5643 (portfolio_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE portfolio (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, cash_reserve TINYINT(1) NOT NULL, allocation_percent NUMERIC(6, 5) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE portfolio_holding ADD CONSTRAINT FK_D0AF7D85DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id)');
        $this->addSql('ALTER TABLE portfolio_holding ADD CONSTRAINT FK_D0AF7D8B96B5643 FOREIGN KEY (portfolio_id) REFERENCES portfolio (id)');
        $this->addSql('ALTER TABLE account_holding RENAME INDEX idx_5bbfd8165da1941 TO IDX_9294CC5E5DA1941');
        $this->addSql('ALTER TABLE account_holding RENAME INDEX idx_5bbfd8169b6b5fba TO IDX_9294CC5E9B6B5FBA');

        // Insert Cash Reserve and default to 5%
        $this->addSql('INSERT INTO portfolio (name, cash_reserve, allocation_percent) VALUES("Cash Reserve", TRUE, 0.05)');

        // Cash Reserve Portfolio should have Cash asset
        $this->addSql(
            'INSERT INTO portfolio_holding ' .
            '(asset_id, portfolio_id, shares, total_value, invested) VALUES(' .
            "(SELECT id FROM asset WHERE symbol = '$' ORDER BY id LIMIT 1), " .
            "(SELECT id FROM PORTFOLIO WHERE cash_reserve IS TRUE ORDER BY id LIMIT 1), " .
            '0, 0, 0)'
        );

        // Create default investing portfolio
        $this->addSql('INSERT INTO portfolio (name, cash_reserve, allocation_percent) VALUES("Stocks", FALSE, NULL)');

        // Stocks portfolio should also have Cash
        $this->addSql(
            'INSERT INTO portfolio_holding ' .
            '(asset_id, portfolio_id, shares, total_value, invested) VALUES(' .
            "(SELECT id FROM asset WHERE symbol = '$' ORDER BY id LIMIT 1), " .
            "(SELECT id FROM PORTFOLIO WHERE name = 'Stocks' ORDER BY id LIMIT 1), " .
            '0, 0, 0)'
        );
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE portfolio_holding DROP FOREIGN KEY FK_D0AF7D8B96B5643');
        $this->addSql('DROP TABLE portfolio_holding');
        $this->addSql('DROP TABLE portfolio');
        $this->addSql('ALTER TABLE account_holding RENAME INDEX idx_9294cc5e5da1941 TO IDX_5BBFD8165DA1941');
        $this->addSql('ALTER TABLE account_holding RENAME INDEX idx_9294cc5e9b6b5fba TO IDX_5BBFD8169B6B5FBA');
    }
}
