<?php

declare(strict_types=1);

namespace Alfred\App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220806010035 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create gif2category table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('gif2category');

        $table->addColumn('gifId', Types::INTEGER)
            ->setComment('Gif ID');

        $table->addColumn('categoryId', Types::INTEGER)
            ->setComment('Category ID');

        $table->setComment('Categories of GIFs');
        $table->setPrimaryKey(['gifId', 'categoryId']);

        $categoryTable = $schema->getTable('category');
        $gifTable = $schema->getTable('gif');

        $table->addIndex(['gifId'], 'K_Gif2category_GifId');
        $table->addIndex(['categoryId'], 'K_Gif2category_Category');

        $table->addForeignKeyConstraint($categoryTable, ['gifId'], ['id'], name: 'FK_Gif2Category_GifId');
        $table->addForeignKeyConstraint($gifTable, ['categoryId'], ['id'], name: 'FK_Gif2Category_Category');
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('gif2category');
    }
}
