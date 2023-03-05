<?php

declare(strict_types=1);

namespace Alfred\App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230216154851 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add relation to request2response';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable('response');

        $table->removeForeignKey('FK_Response_WordId');

        $table->dropIndex('K_Response_WordId');

        $table->dropColumn('wordId');
        $table->dropColumn('wordVariantId');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
