<?php

declare(strict_types=1);

namespace Alfred\App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230311202448 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $query = $this->connection->prepare('UPDATE request set aggressiveness = 1 WHERE aggressiveness = 0 OR aggressiveness IS NULL');
        $query->executeQuery();
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
