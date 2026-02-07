<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260206112010 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE issue ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233E12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_12AD233E12469DE2 ON issue (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE category');
        $this->addSql('ALTER TABLE issue DROP FOREIGN KEY FK_12AD233E12469DE2');
        $this->addSql('DROP INDEX IDX_12AD233E12469DE2 ON issue');
        $this->addSql('ALTER TABLE issue DROP category_id');
    }
}
