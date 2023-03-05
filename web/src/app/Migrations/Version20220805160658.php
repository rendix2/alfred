<?php

declare(strict_types=1);

namespace Alfred\App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220805160658 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create alfred table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('alfred');
        $table->setComment('Alfred');

        $table->addColumn('id', Types::INTEGER)
            ->setComment('ID')
            ->setAutoincrement(true);

        $table->addColumn('isActive', Types::BOOLEAN)
            ->setComment('Is active?');

        $table->addColumn('mood',Types::INTEGER)
            ->setComment('Mood');

        $table->addColumn('startedAt', Types::DATETIME_MUTABLE)
            ->setComment('Started at')
            ->setNotnull(false);

        $table->addColumn('finishedAt', Types::DATETIME_MUTABLE)
            ->setNotnull('Finished at')
            ->setNotnull(false);

        $table->addColumn('createdAt', Types::DATETIME_MUTABLE)
            ->setComment('Created at')
            ->setDefault('CURRENT_TIMESTAMP');

        $table->addColumn('updatedAt', Types::DATETIME_MUTABLE)
            ->setComment('Updated At')
            ->setNotnull(false);

        $table->setPrimaryKey(['id']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('alfred');
    }
}
