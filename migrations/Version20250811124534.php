<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250811124534 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE report (id INT AUTO_INCREMENT NOT NULL, reporter_id INT NOT NULL, reported_trip_id INT NOT NULL, reason VARCHAR(255) NOT NULL, status VARCHAR(50) NOT NULL, INDEX IDX_C42F7784E1CFE6F5 (reporter_id), INDEX IDX_C42F7784235ABF5 (reported_trip_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784E1CFE6F5 FOREIGN KEY (reporter_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784235ABF5 FOREIGN KEY (reported_trip_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE rating ADD trip_id INT NOT NULL');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D8892622A5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id)');
        $this->addSql('CREATE INDEX IDX_D8892622A5BC2E0E ON rating (trip_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784E1CFE6F5');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784235ABF5');
        $this->addSql('DROP TABLE report');
        $this->addSql('ALTER TABLE rating DROP FOREIGN KEY FK_D8892622A5BC2E0E');
        $this->addSql('DROP INDEX IDX_D8892622A5BC2E0E ON rating');
        $this->addSql('ALTER TABLE rating DROP trip_id');
    }
}
