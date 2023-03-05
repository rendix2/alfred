<?php

declare(strict_types=1);

namespace Alfred\App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220806095802 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'create word2Category table';
    }

    public function up(Schema $schema) : void
    {
        $table = $schema->createTable('word2category');

        $table->addColumn('wordId', Types::INTEGER)
            ->setComment('Word ID');

        $table->addColumn('categoryId', Types::INTEGER)
            ->setComment('Category ID');

        $table->addIndex(['wordId'], 'K_Word2Category_WordId');
        $table->addIndex(['categoryId'], 'K_Word2Category_CategoryId');

        $table->addForeignKeyConstraint($schema->getTable('word'), ['wordId'], ['id'], name: 'FK_Word2Category_WordId');
        $table->addForeignKeyConstraint($schema->getTable('category'), ['categoryId'], ['id'], name: 'FK_Word2Category_CategoryId');

        $table->setComment('Categories of Words');
        $table->setPrimaryKey(['wordId', 'categoryId']);
    }

    public function down(Schema $schema) : void
    {
        $schema->dropTable('word2category');
    }
}
