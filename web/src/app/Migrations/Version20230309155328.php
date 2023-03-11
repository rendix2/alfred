<?php

declare(strict_types=1);

namespace Alfred\App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230309155328 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('userPermissionsChat');

        $table->addColumn('userId', Types::INTEGER)
            ->setComment('User ID');

        $table->addColumn('chatId', Types::INTEGER)
            ->setComment('Chat ID');

        $table->setComment('User permissions in chats');
        $table->setPrimaryKey(['userId', 'chatId']);

        $table->addIndex(['userId'], 'K_UserPermissionsChat_UserId');
        $table->addIndex(['chatId'], 'K_UserPermissionsChat_ChatId');

        $table->addForeignKeyConstraint($schema->getTable('user'), ['userId'], ['id'], name: 'FK_UserPermissionsChat_UserId');
        $table->addForeignKeyConstraint($schema->getTable('chat'), ['chatId'], ['id'], name: 'FK_UserPermissionsChat_ChatId');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
