<?php

declare(strict_types=1);

namespace Alfred\App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230211194812 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'create chats';
    }

    public function up(Schema $schema) : void
    {
        $table = $schema->createTable('chat');

        $table->addColumn('id', Types::INTEGER)
            ->setComment('ID')
            ->setAutoincrement(true);

        $table->addColumn('name', Types::STRING)
            ->setComment('Name')
            ->setLength(512);

        $table->addColumn('telegramId', Types::BIGINT)
            ->setComment('Telegram ID');

        $table->addColumn('isActive', Types::BOOLEAN)
            ->setComment('Is active?');

        $table->setComment('Chats');
        $table->setPrimaryKey(['id']);
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
