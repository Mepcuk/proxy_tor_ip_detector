<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211026184413 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE proxy_list_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE proxy_list (id INT NOT NULL, ip VARCHAR(255) NOT NULL, port INT NOT NULL, country_code VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, proxy_anonymity VARCHAR(255) DEFAULT NULL, google_check BOOLEAN DEFAULT NULL, https_check BOOLEAN DEFAULT NULL, protocol VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE proxy_list_id_seq CASCADE');
        $this->addSql('DROP TABLE proxy_list');
    }
}
