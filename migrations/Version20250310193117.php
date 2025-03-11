<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250310193117 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calculation ADD result DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE calculation ADD is_shown BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX calculation_unique_constraint ON calculation (argument_a, argument_b, operation)');
        $this->addSql('CREATE INDEX calulation_is_shown_part_idx ON calculation (is_shown) WHERE is_shown = false');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX calculation_unique_constraint');
        $this->addSql('DROP INDEX calulation_is_shown_part_idx');
        $this->addSql('ALTER TABLE calculation DROP result');
        $this->addSql('ALTER TABLE calculation DROP is_shown');
    }
}
