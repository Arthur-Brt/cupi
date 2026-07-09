<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260708140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add gender_combination to position (constrains which pair of players a position applies to)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE position ADD gender_combination VARCHAR(255) DEFAULT 'any' NOT NULL");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE position DROP gender_combination');
    }
}
