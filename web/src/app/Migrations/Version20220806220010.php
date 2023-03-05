<?php

declare(strict_types=1);

namespace Alfred\App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220806220010 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create responseReactions table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('responseReaction');

        $table->addColumn('id', Types::INTEGER)
            ->setComment('ID')
            ->setAutoincrement(true);

        $table->addColumn('messageId', Types::INTEGER)
            ->setComment('Message ID');

        $table->addColumn('wordId', Types::INTEGER)
           ->setComment('Word ID');

        $table->addColumn('responseId', Types::INTEGER)
            ->setComment('Response ID');

        $table->addColumn('reaction', Types::STRING)
            ->setComment('Reaction')
            ->setLength(10);

        $table->addColumn('createdAt', Types::DATETIME_MUTABLE)
            ->setComment('Created at')
            ->setDefault('CURRENT_TIMESTAMP');

        $table->addColumn('updatedAt', Types::DATETIME_MUTABLE)
            ->setComment('Updated At')
            ->setNotnull(false);

        $table->setPrimaryKey(['id']);
        $table->setComment('Response Reaction');

        $table->addIndex(['messageId'], 'K_ResponseReaction_MessageId');
        $table->addIndex(['wordId'], 'K_ResponseReaction_WordId');
        $table->addIndex(['responseId'], 'K_ResponseReaction_ResponseId');

        $table->addForeignKeyConstraint($schema->getTable('word'), ['wordId'], ['id'], name: 'FK_ResponseReaction_WordId');
        $table->addForeignKeyConstraint($schema->getTable('response'), ['responseId'], ['id'], name: 'FK_ResponseReaction_ResponseId');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
