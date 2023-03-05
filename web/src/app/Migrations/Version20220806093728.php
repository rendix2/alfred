<?php

declare(strict_types=1);

namespace Alfred\App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220806093728 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create poll table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('poll');

        $table->addColumn('id', Types::INTEGER)
            ->setComment('ID')
            ->setAutoincrement(true);

        $table->addColumn('question', Types::STRING)
            ->setComment('Question')
            ->setLength(512);

        $table->addColumn('type', Types::STRING)
            ->setDefault('regular')
            ->setComment('type');

        $table->addColumn('allowsMultipleAnswers', Types::BOOLEAN)
            ->setComment('Is multiple?');

        $table->addColumn('createdAt', Types::DATETIME_MUTABLE)
            ->setComment('Created at')
            ->setDefault('CURRENT_TIMESTAMP');

        $table->addColumn('updatedAt', Types::DATETIME_MUTABLE)
            ->setComment('Updated At')
            ->setNotnull(false);

        $table->setPrimaryKey(['id']);
        $table->setComment('Poll');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
