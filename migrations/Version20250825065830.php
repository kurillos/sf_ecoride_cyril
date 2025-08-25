<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250825065830 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C62F3E1E57');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6ED5CA9E6');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6A76ED395');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP INDEX UNIQ_8D93D64986CC499D ON `user`');
        $this->addSql('ALTER TABLE `user` ADD last_login DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD car_model VARCHAR(255) DEFAULT NULL, ADD license_plate VARCHAR(255) DEFAULT NULL, ADD phone_number VARCHAR(255) DEFAULT NULL, DROP pseudo, DROP desired_role, CHANGE credits credits INT NOT NULL');
        $this->addSql('ALTER TABLE `user` RENAME INDEX uniq_identifier_email TO UNIQ_8D93D649E7927C74');
        $this->addSql('ALTER TABLE service ADD review_ids JSON DEFAULT NULL');
    }
}
