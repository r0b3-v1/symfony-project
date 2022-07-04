<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220704173243 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commission DROP FOREIGN KEY FK_1C65015819EB6921');
        $this->addSql('ALTER TABLE commission DROP FOREIGN KEY FK_1C650158F6203804');
        $this->addSql('ALTER TABLE commission DROP FOREIGN KEY FK_1C65015812469DE2');
        $this->addSql('ALTER TABLE commission DROP FOREIGN KEY FK_1C650158B7970CF8');
        $this->addSql('ALTER TABLE commission ADD CONSTRAINT FK_1C65015819EB6921 FOREIGN KEY (client_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE commission ADD CONSTRAINT FK_1C650158F6203804 FOREIGN KEY (statut_id) REFERENCES commission_statut (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE commission ADD CONSTRAINT FK_1C65015812469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE commission ADD CONSTRAINT FK_1C650158B7970CF8 FOREIGN KEY (artist_id) REFERENCES user (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commission DROP FOREIGN KEY FK_1C65015819EB6921');
        $this->addSql('ALTER TABLE commission DROP FOREIGN KEY FK_1C650158B7970CF8');
        $this->addSql('ALTER TABLE commission DROP FOREIGN KEY FK_1C65015812469DE2');
        $this->addSql('ALTER TABLE commission DROP FOREIGN KEY FK_1C650158F6203804');
        $this->addSql('ALTER TABLE commission ADD CONSTRAINT FK_1C65015819EB6921 FOREIGN KEY (client_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commission ADD CONSTRAINT FK_1C650158B7970CF8 FOREIGN KEY (artist_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commission ADD CONSTRAINT FK_1C65015812469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE commission ADD CONSTRAINT FK_1C650158F6203804 FOREIGN KEY (statut_id) REFERENCES commission_statut (id)');
    }
}
