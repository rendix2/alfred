<?php

declare(strict_types=1);

namespace Alfred\App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220806104721 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create responseHistory table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('responseHistory');

        $table->addColumn('id', Types::INTEGER)
            ->setComment('ID')
            ->setAutoincrement(true);

        $table->addColumn('messageId', Types::INTEGER)
            ->setComment('Telegram Message ID');

        $table->addColumn('wordId', Types::INTEGER)
            ->setComment('Word ID');

        $table->addColumn('responseId', Types::INTEGER)
            ->setComment('Response ID');

        $table->addColumn('createdAt', Types::DATETIME_MUTABLE)
            ->setComment('Created at')
            ->setDefault('CURRENT_TIMESTAMP');

        $table->addColumn('updatedAt', Types::DATETIME_MUTABLE)
            ->setComment('Updated At')
            ->setNotnull(false);

        $table->setPrimaryKey(['id']);
        $table->setComment('Response History');

        $table->addIndex(['messageId'], 'K_ResponseHistory_MessageId');
        $table->addIndex(['wordId'], 'K_ResponseHistory_WordId');
        $table->addIndex(['responseId'], 'K_ResponseHistory_ResponseId');

        $table->addForeignKeyConstraint($schema->getTable('word'), ['wordId'], ['id'], name: 'FK_ResponseHistory_WordId');
        $table->addForeignKeyConstraint($schema->getTable('response'), ['responseId'], ['id'], name: 'FK_ResponseHistory_ResponseId');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
