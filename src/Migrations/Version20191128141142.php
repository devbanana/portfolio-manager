<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191128141142 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create assets and holdings';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE holding (id INT AUTO_INCREMENT NOT NULL, asset_id INT NOT NULL, account_id INT NOT NULL, shares NUMERIC(15, 8) NOT NULL, total_value NUMERIC(10, 2) NOT NULL, invested NUMERIC(10, 2) NOT NULL, INDEX IDX_5BBFD8165DA1941 (asset_id), INDEX IDX_5BBFD8169B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE asset (id INT AUTO_INCREMENT NOT NULL, asset_class_id INT NOT NULL, symbol VARCHAR(10) NOT NULL, name VARCHAR(255) DEFAULT NULL, is_fractional TINYINT(1) NOT NULL, market_price NUMERIC(10, 4) DEFAULT NULL, day_change NUMERIC(12, 8) DEFAULT NULL, day_change_percent NUMERIC(6, 5) DEFAULT NULL, INDEX IDX_2AF5A5C686B1190 (asset_class_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE holding ADD CONSTRAINT FK_5BBFD8165DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id)');
        $this->addSql('ALTER TABLE holding ADD CONSTRAINT FK_5BBFD8169B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE asset ADD CONSTRAINT FK_2AF5A5C686B1190 FOREIGN KEY (asset_class_id) REFERENCES asset_class (id)');

        $this->addSql(
            'INSERT INTO asset (symbol, name, asset_class_id, is_fractional, market_price) VALUES' .
            "('$', 'Cash', (SELECT id FROM asset_class WHERE name = 'Cash'), 1, 1)"
        );
        $this->addSql(
            'INSERT INTO holding (asset_id, account_id, shares, total_value, invested) VALUES' .
            "((SELECT id FROM asset WHERE symbol = '$'), " .
            "(SELECT id FROM account WHERE name = 'Cash'), " .
            "0, 0, 0)"
        );
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE holding DROP FOREIGN KEY FK_5BBFD8165DA1941');
        $this->addSql('DROP TABLE holding');
        $this->addSql('DROP TABLE asset');
    }
}
