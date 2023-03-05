<?php

declare(strict_types=1);

namespace Alfred\App\Migrations;

use Alfred\App\Model\Tables;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230304174054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable(Tables::GIF_TABLE);

        $table->addColumn('name', Types::STRING)
            ->setComment('Name');

        $table->addColumn('description', Types::TEXT)
            ->setComment('Description')
            ->setNotnull(false);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
