<?php

declare(strict_types=1);

namespace Alfred\App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220806095054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create word table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('word');

        $table->addColumn('id', Types::INTEGER)
            ->setComment('ID')
            ->setAutoincrement(true);

        $table->addColumn('wordText', Types::STRING)
            ->setComment('Word text')
            ->setLength(512);

        $table->addColumn('regularExpression', Types::TEXT)
            ->setComment('RE');

        $table->addColumn('description', Types::TEXT)
            ->setComment('Description')
            ->setNotnull(false)
            ->setDefault(null);

        $table->addColumn('gender', Types::STRING)
            ->setComment('Gender')
            ->setLength(10)
            ->setDefault('both');

        $table->addColumn('priority', Types::INTEGER)
            ->setComment('Priority');

        $table->addColumn('isActive', Types::BOOLEAN)
            ->setComment('Is active?');

        $table->addColumn('isExplicit', Types::BOOLEAN)
            ->setComment('Is explicit?');

        $table->addColumn('createdAt', Types::DATETIME_MUTABLE)
            ->setComment('Created at')
            ->setDefault('CURRENT_TIMESTAMP');

        $table->addColumn('updatedAt', Types::DATETIME_MUTABLE)
            ->setComment('Updated At')
            ->setNotnull(false);

        $table->setPrimaryKey(['id']);
        $table->setComment('Word');
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('word');
    }
}
