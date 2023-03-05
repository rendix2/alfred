<?php

declare(strict_types=1);

namespace Alfred\App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220806101715 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create response table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('response');

        $table->addColumn('id', Types::INTEGER)
            ->setComment('ID')
            ->setAutoincrement(true);

        $table->addColumn('wordId', Types::INTEGER)
            ->setComment('Word ID');

/*        $table->addColumn('commandId', Types::INTEGER)
            ->setComment('Command ID')
            ->setNotnull(false);*/

        $table->addColumn('wordVariantId', Types::INTEGER)
            ->setComment('Word variant ID')
            ->setNotnull(false);

        $table->addColumn('answerId', Types::INTEGER)
            ->setComment('Answer ID')
            ->setNotnull(false);

        $table->addColumn('pollId', Types::INTEGER)
            ->setComment('Poll ID')
            ->setNotnull(false);

        $table->addColumn('gifId', Types::INTEGER)
            ->setComment('GIF ID')
            ->setNotnull(false);

        $table->addColumn('locationId', Types::INTEGER)
            ->setComment('location ID')
            ->setNotnull(false);

        $table->addColumn('isActive', Types::BOOLEAN)
            ->setComment('Is active?');

        $table->addColumn('isExplicit', Types::BOOLEAN)
            ->setComment('Is explicit?');

        $table->addColumn('priority', Types::INTEGER)
            ->setComment('Priority');

        $table->addColumn('aggressiveness', Types::INTEGER)
            ->setComment('Aggressiveness');

        $table->addColumn('createdAt', Types::DATETIME_MUTABLE)
            ->setComment('Created at')
            ->setDefault('CURRENT_TIMESTAMP');

        $table->addColumn('updatedAt', Types::DATETIME_MUTABLE)
            ->setComment('Updated At')
            ->setNotnull(false);

        $table->setPrimaryKey(['id']);
        $table->setComment('Response');

        $table->addIndex(['wordId'], 'K_Response_WordId');
        //$table->addIndex(['commandId'], 'K_Response_CommandId');
        $table->addIndex(['answerId'], 'K_Response_AnswerId');
        $table->addIndex(['pollId'], 'K_Response_PollId');
        $table->addIndex(['gifId'], 'K_Response_GifId');
        $table->addIndex(['locationId'], 'K_Response_LocationId');

        $table->addForeignKeyConstraint($schema->getTable('word'), ['wordId'], ['id'], name: 'FK_Response_WordId');
        $table->addForeignKeyConstraint($schema->getTable('answer'), ['answerId'], ['id'], name: 'FK_Response_AnswerId');
        $table->addForeignKeyConstraint($schema->getTable('poll'), ['pollId'], ['id'], name: 'FK_Response_PollId');
        $table->addForeignKeyConstraint($schema->getTable('gif'), ['gifId'], ['id'], name: 'FK_Response_GifId');
        $table->addForeignKeyConstraint($schema->getTable('location'), ['locationId'], ['id'], name: 'FK_Response_LocationId');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
