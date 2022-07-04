<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220704173435 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE submission DROP FOREIGN KEY FK_DB055AF3F675F31B');
        $this->addSql('ALTER TABLE submission DROP FOREIGN KEY FK_DB055AF312469DE2');
        $this->addSql('ALTER TABLE submission CHANGE author_id author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE submission ADD CONSTRAINT FK_DB055AF3F675F31B FOREIGN KEY (author_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE submission ADD CONSTRAINT FK_DB055AF312469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE submission DROP FOREIGN KEY FK_DB055AF3F675F31B');
        $this->addSql('ALTER TABLE submission DROP FOREIGN KEY FK_DB055AF312469DE2');
        $this->addSql('ALTER TABLE submission CHANGE author_id author_id INT NOT NULL');
        $this->addSql('ALTER TABLE submission ADD CONSTRAINT FK_DB055AF3F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE submission ADD CONSTRAINT FK_DB055AF312469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
    }
}
