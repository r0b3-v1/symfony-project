<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220702144458 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commission (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, artist_id INT DEFAULT NULL, category_id INT DEFAULT NULL, statut_id INT DEFAULT NULL, description LONGTEXT NOT NULL, price DOUBLE PRECISION NOT NULL, deadline DATE DEFAULT NULL, INDEX IDX_1C65015819EB6921 (client_id), INDEX IDX_1C650158B7970CF8 (artist_id), INDEX IDX_1C65015812469DE2 (category_id), INDEX IDX_1C650158F6203804 (statut_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commission_statut (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commission ADD CONSTRAINT FK_1C65015819EB6921 FOREIGN KEY (client_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commission ADD CONSTRAINT FK_1C650158B7970CF8 FOREIGN KEY (artist_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commission ADD CONSTRAINT FK_1C65015812469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE commission ADD CONSTRAINT FK_1C650158F6203804 FOREIGN KEY (statut_id) REFERENCES commission_statut (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commission DROP FOREIGN KEY FK_1C650158F6203804');
        $this->addSql('DROP TABLE commission');
        $this->addSql('DROP TABLE commission_statut');
    }
}
