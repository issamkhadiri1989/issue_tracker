<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260206210018 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233EE1CFE6F5 FOREIGN KEY (reporter_id) REFERENCES `member` (id)');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233EEB7B697B FOREIGN KEY (assingee_id) REFERENCES `member` (id)');
        $this->addSql('CREATE INDEX IDX_12AD233EE1CFE6F5 ON issue (reporter_id)');
        $this->addSql('CREATE INDEX IDX_12AD233EEB7B697B ON issue (assingee_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE issue DROP FOREIGN KEY FK_12AD233EE1CFE6F5');
        $this->addSql('ALTER TABLE issue DROP FOREIGN KEY FK_12AD233EEB7B697B');
        $this->addSql('DROP INDEX IDX_12AD233EE1CFE6F5 ON issue');
        $this->addSql('DROP INDEX IDX_12AD233EEB7B697B ON issue');
    }
}
