<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260209213913 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE issue (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(100) NOT NULL, description LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, severity INT DEFAULT 0 NOT NULL, status VARCHAR(255) DEFAULT \'backlog\' NOT NULL, category_id INT DEFAULT NULL, reporter_id INT DEFAULT NULL, assignee_id INT DEFAULT NULL, INDEX IDX_12AD233E12469DE2 (category_id), INDEX IDX_12AD233EE1CFE6F5 (reporter_id), INDEX IDX_12AD233E59EC7D60 (assignee_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `member` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, full_name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233E12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233EE1CFE6F5 FOREIGN KEY (reporter_id) REFERENCES `member` (id)');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233E59EC7D60 FOREIGN KEY (assignee_id) REFERENCES `member` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE issue DROP FOREIGN KEY FK_12AD233E12469DE2');
        $this->addSql('ALTER TABLE issue DROP FOREIGN KEY FK_12AD233EE1CFE6F5');
        $this->addSql('ALTER TABLE issue DROP FOREIGN KEY FK_12AD233E59EC7D60');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE issue');
        $this->addSql('DROP TABLE `member`');
    }
}
