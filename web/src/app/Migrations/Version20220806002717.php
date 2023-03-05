<?php

declare(strict_types=1);

namespace Alfred\App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220806002717 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'create answer table';
    }

    public function up(Schema $schema) : void
    {
        $table = $schema->createTable('answer');

        $table->setComment('Answer');

        $table->addColumn('id', Types::INTEGER)
            ->setComment('ID')
            ->setAutoincrement(true);

        $table->addColumn('answerText', Types::TEXT)
            ->setComment('Text');

        $table->addColumn('priority', Types::INTEGER)
            ->setComment('Priority');

        $table->addColumn('aggressiveness', Types::INTEGER)
            ->setComment('Aggressiveness');

        $table->addColumn('isActive', Types::BOOLEAN)
            ->setComment('Is active?');

        $table->addColumn('isExplicit', Types::BOOLEAN)
            ->setComment('Is explicit?');

        $table->addColumn('createdAt', Types::DATETIME_MUTABLE)
            ->setComment('Created at')
            ->setDefault('CURRENT_TIMESTAMP');

        $table->addColumn('updatedAt', Types::DATETIME_MUTABLE)
            ->setComment('Updated at')
            ->setNotnull(false);

        $table->setPrimaryKey(['id']);
    }

    public function down(Schema $schema) : void
    {
        $schema->dropTable('answer');
    }
}
