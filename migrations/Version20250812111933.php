<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250812111933 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE report ADD reported_user_id INT NOT NULL');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784E7566E FOREIGN KEY (reported_user_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_C42F7784E7566E ON report (reported_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784E7566E');
        $this->addSql('DROP INDEX IDX_C42F7784E7566E ON report');
        $this->addSql('ALTER TABLE report DROP reported_user_id');
    }
}
