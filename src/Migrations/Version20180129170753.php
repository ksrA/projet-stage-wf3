<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180129170753 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE application (id INT AUTO_INCREMENT NOT NULL, id_reunion INT NOT NULL, campus VARCHAR(255) NOT NULL, first_name VARCHAR(30) NOT NULL, last_name VARCHAR(30) NOT NULL, phone_number VARCHAR(255) NOT NULL, birthday DATETIME NOT NULL, nationality VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, resume VARCHAR(255) NOT NULL, cover_letter VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, city_pole_emploi VARCHAR(255) NOT NULL, situation VARCHAR(255) NOT NULL, year_of_last_degree INT NOT NULL, last_formation VARCHAR(255) NOT NULL, note INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE inscription (id INT AUTO_INCREMENT NOT NULL, last_name VARCHAR(30) NOT NULL, first_name VARCHAR(30) NOT NULL, phone_number VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, locality VARCHAR(255) NOT NULL, date_inscription DATETIME NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE session_formation (id INT AUTO_INCREMENT NOT NULL, campus VARCHAR(255) NOT NULL, date_reunion DATETIME NOT NULL, date_session DATETIME NOT NULL, financer VARCHAR(255) NOT NULL, hash_link VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(30) NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', is_active TINYINT(1) NOT NULL, reset_hash VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_C2502824F85E0677 (username), UNIQUE INDEX UNIQ_C2502824E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE application');
        $this->addSql('DROP TABLE inscription');
        $this->addSql('DROP TABLE session_formation');
        $this->addSql('DROP TABLE app_users');
    }
}
