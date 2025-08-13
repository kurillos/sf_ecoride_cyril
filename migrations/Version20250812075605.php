<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250812075605 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784235ABF5');
        $this->addSql('ALTER TABLE report ADD contact_email VARCHAR(255) NOT NULL, ADD contact_phone VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784235ABF5 FOREIGN KEY (reported_trip_id) REFERENCES trip (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784235ABF5');
        $this->addSql('ALTER TABLE report DROP contact_email, DROP contact_phone');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784235ABF5 FOREIGN KEY (reported_trip_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
