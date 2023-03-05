<?php

declare(strict_types=1);

namespace Alfred\App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230215203600 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'create';
    }

    public function up(Schema $schema) : void
    {
        $table = $schema->createTable('request');

        $table->addColumn('id', Types::INTEGER)
            ->setComment('ID')
            ->setAutoincrement(true);

        $table->addColumn('wordId', Types::INTEGER)
            ->setComment('Word ID');

        $table->addColumn('wordVariantId', Types::INTEGER)
            ->setComment('Word Variant ID')
            ->setNotnull(false);

        $table->addColumn('chatId', Types::INTEGER)
            ->setComment('Chat ID');

        $table->addColumn('eventId', Types::INTEGER)
            ->setComment('Event ID');

        $table->addColumn('createdAt', Types::DATETIME_MUTABLE)
            ->setComment('Created at')
            ->setDefault('CURRENT_TIMESTAMP');

        $table->addColumn('updatedAt', Types::DATETIME_MUTABLE)
            ->setComment('Updated At')
            ->setNotnull(false);

        $table->setPrimaryKey(['id']);

        $table->addIndex(['wordId'], 'K_KeyWord_WordId');
        $table->addIndex(['wordVariantId'], 'K_KeyWord_WordVariantId');
        $table->addIndex(['chatId'], 'K_KeyWord_ChatId');
        $table->addIndex(['eventId'], 'K_KeyWord_EventId');

        $table->addUniqueIndex(['wordId', 'wordVariantId', 'chatId', 'eventId'], 'K_Request_Request');

        $table->addForeignKeyConstraint(
            $schema->getTable('word'),
            ['wordId'],
            ['id'],
            name: 'FK_KeyWord_WordId'
        );

        $table->addForeignKeyConstraint(
            $schema->getTable('wordVariant'),
            ['wordVariantId'],
            ['id'],
            name: 'FK_KeyWord_WordVariantId'
        );

        $table->addForeignKeyConstraint(
            $schema->getTable('chat'),
            ['chatId'],
            ['id'],
            name: 'FK_KeyWord_ChatId'
        );

        $table->addForeignKeyConstraint(
            $schema->getTable('event'),
            ['eventId'],
            ['id'],
            name: 'FK_KeyWord_EventId'
        );
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
