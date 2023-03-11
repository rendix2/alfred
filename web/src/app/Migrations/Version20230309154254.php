<?php

declare(strict_types=1);

namespace Alfred\App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230309154254 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('user');

        $table->addColumn('id', Types::INTEGER)
            ->setAutoincrement(true)
            ->setComment('ID');

        $table->addColumn('name', Types::STRING)
            ->setComment('Name')
            ->setLength(512);

        $table->addColumn('surname', Types::STRING)
            ->setComment('Surname')
            ->setLength(512);

        $table->addColumn('username', Types::STRING)
            ->setComment('Username')
           ->setLength(512);

        $table->addColumn('password', Types::STRING)
            ->setComment('Password')
            ->setLength(1024);

        $table->setPrimaryKey(['id']);
        $table->setComment('Users');
    }

    public function down(Schema $schema): void
    {
    }
}
