<?php

declare(strict_types=1);

namespace Alfred\App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230219092643 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable('responseHistory');

        $table->removeForeignKey('FK_ResponseHistory_WordId');
        $table->dropIndex('K_ResponseHistory_WordId');
        $table->dropColumn('wordId');

        $table->addColumn('requestId', Types::INTEGER)
            ->setComment('Request ID');

        $table->addIndex(['requestId'], 'K_ResponseHistory_RequestId');
        $table->addForeignKeyConstraint($schema->getTable('request'), ['requestId'], ['id'],name: 'K_ResponseHistory_RequestId');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
