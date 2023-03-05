<?php

declare(strict_types=1);

namespace Alfred\App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230211193858 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create events';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('event');

        $table->addColumn('id', Types::INTEGER)
            ->setAutoincrement(true)
            ->setComment('ID');

        $table->addColumn('name', Types::STRING)
            ->setComment('Name')
            ->setLength(512);

        $table->addColumn('description', Types::TEXT)
            ->setComment('Description')
            ->setNotnull(false);

        $table->addColumn('isActive', Types::BOOLEAN)
            ->setComment('Is active?');

        $table->addColumn('activeFrom', Types::DATETIME_MUTABLE)
            ->setComment('Active from')
            ->setNotnull(false)
            ->setDefault(null);

        $table->addColumn('activeTo', Types::DATETIME_MUTABLE)
            ->setComment('Active to')
            ->setNotnull(false)
            ->setDefault(null);

        $table->setPrimaryKey(['id']);
        $table->setComment('Events');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
