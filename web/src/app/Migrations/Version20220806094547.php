<?php

declare(strict_types=1);

namespace Alfred\App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220806094547 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create poll_option table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('pollOption');

        $table->addColumn('id', Types::INTEGER)
            ->setComment('ID')
            ->setAutoincrement(true);

        $table->addColumn('pollId', Types::INTEGER)
            ->setComment('Poll ID');

        $table->addColumn('optionText', Types::TEXT)
            ->setComment('Option text');

        $table->addColumn('createdAt', Types::DATETIME_MUTABLE)
            ->setComment('Created at')
            ->setDefault('CURRENT_TIMESTAMP');

        $table->addColumn('updatedAt', Types::DATETIME_MUTABLE)
            ->setComment('Updated At')
            ->setNotnull(false);

        $table->addIndex(['pollId'], 'K_Poll_option_PollId');

        $table->addForeignKeyConstraint($schema->getTable('poll'), ['pollID'], ['id'], name: 'FK_Poll_option_PollId');

        $table->setPrimaryKey(['id']);
        $table->setComment('Poll option');
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('pollOption');
    }
}
