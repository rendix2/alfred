<?php

declare(strict_types=1);

namespace Alfred\App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220806100437 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create location table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('location');

        $table->addColumn('id', Types::INTEGER)
            ->setComment('ID')
            ->setAutoincrement(true);

        $table->addColumn('latitude', Types::FLOAT)
            ->setComment('Latitude');

        $table->addColumn('longitude', Types::FLOAT)
            ->setComment('longitude');

        $table->addColumn('name', Types::STRING)
            ->setComment('Name')
            ->setLength(512);

        $table->addColumn('description', Types::TEXT)
            ->setComment('Description');

        $table->addColumn('createdAt', Types::DATETIME_MUTABLE)
            ->setComment('Created at')
            ->setDefault('CURRENT_TIMESTAMP');

        $table->addColumn('updatedAt', Types::DATETIME_MUTABLE)
            ->setComment('Updated At')
            ->setNotnull(false);

        $table->setPrimaryKey(['id']);
        $table->setComment('Location');
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('location');
    }
}
