<?php

declare(strict_types=1);

namespace Alfred\App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220806101049 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'create wordVariant table';
    }

    public function up(Schema $schema) : void
    {
        $table = $schema->createTable('wordVariant');

        $table->addColumn('id', Types::INTEGER)
            ->setComment('ID')
            ->setAutoincrement(true);

        $table->addColumn('wordId', Types::INTEGER)
            ->setComment('word ID');

        $table->addColumn('variantText', Types::TEXT)
            ->setComment('Text');

        $table->addColumn('isTypo', Types::BOOLEAN)
            ->setComment('Is typo?');

        $table->addColumn('isReplacedY', Types::BOOLEAN)
            ->setComment('Is replaced Y?');

        $table->addColumn('isMissingJ', Types::BOOLEAN)
            ->setComment('Is missing J?');

        $table->addColumn('isSynonymous', Types::BOOLEAN)
            ->setComment('Is synonymous?');

        $table->addColumn('createdAt', Types::DATETIME_MUTABLE)
            ->setComment('Created at')
            ->setDefault('CURRENT_TIMESTAMP');

        $table->addColumn('updatedAt', Types::DATETIME_MUTABLE)
            ->setComment('Updated At')
            ->setNotnull(false);

        $table->setPrimaryKey(['id']);
        $table->setComment('wordVariant');
    }

    public function down(Schema $schema) : void
    {
        $schema->dropTable('response');
    }
}
