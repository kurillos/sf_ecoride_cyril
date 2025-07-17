<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250717102439 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE trip (id INT AUTO_INCREMENT NOT NULL, driver_id INT NOT NULL, vehicle_id INT DEFAULT NULL, departure_location VARCHAR(255) NOT NULL, destination_location VARCHAR(255) NOT NULL, departure_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', arrival_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_seats INT NOT NULL, price_per_seat DOUBLE PRECISION NOT NULL, description LONGTEXT DEFAULT NULL, is_smoking_allowed TINYINT(1) NOT NULL, are_animals_allowed TINYINT(1) NOT NULL, status VARCHAR(50) NOT NULL, INDEX IDX_7656F53BC3423909 (driver_id), INDEX IDX_7656F53B545317D1 (vehicle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53BC3423909 FOREIGN KEY (driver_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53B545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicle (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trip DROP FOREIGN KEY FK_7656F53BC3423909');
        $this->addSql('ALTER TABLE trip DROP FOREIGN KEY FK_7656F53B545317D1');
        $this->addSql('DROP TABLE trip');
    }
}
