<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260708130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add missing updated_at column to position table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE position ADD updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE position DROP updated_at');
    }
}
