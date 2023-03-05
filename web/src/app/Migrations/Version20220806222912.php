<?php

declare(strict_types=1);

namespace Alfred\App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220806222912 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'remove redundant data from answer';
    }

    public function up(Schema $schema): void
    {
       $table = $schema->getTable('answer');

       $table->dropColumn('priority');
       $table->dropColumn('aggressiveness');
       $table->dropColumn('isActive');
       $table->dropColumn('isExplicit');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
