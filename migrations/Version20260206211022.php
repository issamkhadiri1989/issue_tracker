<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260206211022 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE issue DROP FOREIGN KEY `FK_12AD233EEB7B697B`');
        $this->addSql('DROP INDEX IDX_12AD233EEB7B697B ON issue');
        $this->addSql('ALTER TABLE issue CHANGE assingee_id assignee_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233E59EC7D60 FOREIGN KEY (assignee_id) REFERENCES `member` (id)');
        $this->addSql('CREATE INDEX IDX_12AD233E59EC7D60 ON issue (assignee_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE issue DROP FOREIGN KEY FK_12AD233E59EC7D60');
        $this->addSql('DROP INDEX IDX_12AD233E59EC7D60 ON issue');
        $this->addSql('ALTER TABLE issue CHANGE assignee_id assingee_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT `FK_12AD233EEB7B697B` FOREIGN KEY (assingee_id) REFERENCES `member` (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_12AD233EEB7B697B ON issue (assingee_id)');
    }
}
