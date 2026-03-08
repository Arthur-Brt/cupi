<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250802145727 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE position (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, intensity VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE position_accessories (position_id INT NOT NULL, accessories_id INT NOT NULL, INDEX IDX_CC9C444CDD842E46 (position_id), INDEX IDX_CC9C444C35D022EB (accessories_id), PRIMARY KEY(position_id, accessories_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE position_accessories ADD CONSTRAINT FK_CC9C444CDD842E46 FOREIGN KEY (position_id) REFERENCES position (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE position_accessories ADD CONSTRAINT FK_CC9C444C35D022EB FOREIGN KEY (accessories_id) REFERENCES accessories (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE position_accessories DROP FOREIGN KEY FK_CC9C444CDD842E46');
        $this->addSql('ALTER TABLE position_accessories DROP FOREIGN KEY FK_CC9C444C35D022EB');
        $this->addSql('DROP TABLE position');
        $this->addSql('DROP TABLE position_accessories');
    }
}
