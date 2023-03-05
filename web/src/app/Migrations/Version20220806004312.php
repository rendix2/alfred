<?php

declare(strict_types=1);

namespace Alfred\App\Migrations;

use Dibi\Type;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220806004312 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'create category table';
    }

    public function up(Schema $schema) : void
    {
        $table = $schema->createTable('category');

        $table->setComment('Category');

        $table->addColumn('id', Types::INTEGER)
            ->setComment('ID')
            ->setAutoincrement(true);

        $table->addColumn('parentId', Types::INTEGER)
            ->setComment('Parent ID')
            ->setNotnull(false)
            ->setDefault(null);

        $table->addColumn('name', Types::STRING)
            ->setLength(512)
            ->setComment('Name');

        $table->addColumn('isActive', Types::BOOLEAN)
            ->setComment('Is active?');

        $table->addColumn('createdAt', Types::DATETIME_MUTABLE)
            ->setComment('Created at')
            ->setDefault('CURRENT_TIMESTAMP');

        $table->addColumn('updatedAt', Types::DATETIME_MUTABLE)
            ->setComment('Updated At')
            ->setNotnull(false);

        $table->setPrimaryKey(['id']);

        $table->addIndex(['parentId'], 'K_Category_ParentId');

        $table->addForeignKeyConstraint($table, ['parentId'], ['id'], [], 'FK_Category_ParentId');
    }

    public function down(Schema $schema) : void
    {
        $schema->dropTable('category');
    }
}
