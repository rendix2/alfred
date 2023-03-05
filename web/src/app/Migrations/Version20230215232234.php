<?php

declare(strict_types=1);

namespace Alfred\App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230215232234 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('request2response');

        $table->addColumn('requestId', Types::INTEGER)
            ->setComment('Request ID');

        $table->addColumn('responseId', Types::INTEGER)
            ->setComment('Response ID');

        $table->setPrimaryKey(['requestId', 'responseId'], 'K_Request2response');

        $table->addIndex(['requestId'], 'K_Request2response_RequestId');
        $table->addIndex(['responseId'], 'K_Request2response_responseId');

        $table->addForeignKeyConstraint($schema->getTable('request'), ['requestId'], ['id'], name: 'FK_Request2response_RequestId');
        $table->addForeignKeyConstraint($schema->getTable('response'), ['responseId'], ['id'], name: 'FK_Request2response_responseId');

        $table->setComment('Requests of Responses');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
