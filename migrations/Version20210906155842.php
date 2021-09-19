<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210906155842 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql('ALTER TABLE reference ADD filename VARCHAR(255) DEFAULT NULL, ADD original_filename VARCHAR(255) DEFAULT NULL, ADD mimetype VARCHAR(255) DEFAULT NULL');

    }
    public function down(Schema $schema): void
    {

        $this->addSql('ALTER TABLE reference DROP filename, DROP original_filename, DROP mimetype');

    }
}
