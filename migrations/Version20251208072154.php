<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251208072154 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // First, create the donneur table
        $this->addSql('CREATE TABLE donneur (id_donneur INT AUTO_INCREMENT NOT NULL, n_cristal VARCHAR(50) DEFAULT NULL, g_sanguin VARCHAR(50) DEFAULT NULL, sexe TINYINT(1) DEFAULT NULL, age DATE DEFAULT NULL, poids VARCHAR(50) DEFAULT NULL, commentaire_patient VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id_donneur)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        
        // Then create other tables
        $this->addSql('CREATE TABLE profil (id_profil INT AUTO_INCREMENT NOT NULL, role VARCHAR(50) NOT NULL, PRIMARY KEY(id_profil)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id_utilisateur INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, ville_res VARCHAR(50) DEFAULT NULL, cp VARCHAR(50) DEFAULT NULL, UNIQUE INDEX UNIQ_1D1C63B3E7927C74 (email), PRIMARY KEY(id_utilisateur)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE patient (id_patient INT AUTO_INCREMENT NOT NULL, id_utilisateur INT NOT NULL, ndossier VARCHAR(50) NOT NULL, nom VARCHAR(50) DEFAULT NULL, prenom VARCHAR(50) DEFAULT NULL, date_naissance DATE DEFAULT NULL, cp VARCHAR(50) DEFAULT NULL, ville_res VARCHAR(50) DEFAULT NULL, INDEX IDX_1ADAD7EB50EAE44 (id_utilisateur), PRIMARY KEY(id_patient)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE greffe (id_greffon INT AUTO_INCREMENT NOT NULL, id_donneur INT NOT NULL, id_patient INT NOT NULL, date_greffe DATE DEFAULT NULL, rang_greffe INT DEFAULT NULL, type_donneur VARCHAR(50) DEFAULT NULL, type_greffe VARCHAR(50) DEFAULT NULL, greffon_fonctionnel TINYINT(1) DEFAULT NULL, date_heure_fin DATETIME DEFAULT NULL, cause_fin_fonct_greffe LONGTEXT DEFAULT NULL, date_declampage DATE DEFAULT NULL, heure_declampage TIME DEFAULT NULL, cote_prelevement_rein VARCHAR(50) DEFAULT NULL, cote_transplantation_rein VARCHAR(50) DEFAULT NULL, ischemie_total TIME DEFAULT NULL, duree_anastomoses INT DEFAULT NULL, sonde_jj TINYINT(1) DEFAULT NULL, commentaire LONGTEXT DEFAULT NULL, compte_rendu_operatoire LONGTEXT DEFAULT NULL, protocole TINYINT(1) DEFAULT NULL, dialyse TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_69FCC4BB9A0E7E03 (id_donneur), INDEX IDX_69FCC4BBC4477E9B (id_patient), PRIMARY KEY(id_greffon)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE etre (id_utilisateur INT NOT NULL, id_profil INT NOT NULL, INDEX IDX_5E95960250EAE44 (id_utilisateur), INDEX IDX_5E959602C0E1077A (id_profil), PRIMARY KEY(id_utilisateur, id_profil)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        
        // Now add foreign key constraints
        $this->addSql('ALTER TABLE greffe ADD CONSTRAINT FK_69FCC4BB9A0E7E03 FOREIGN KEY (id_donneur) REFERENCES donneur (id_donneur)');
        $this->addSql('ALTER TABLE greffe ADD CONSTRAINT FK_69FCC4BBC4477E9B FOREIGN KEY (id_patient) REFERENCES patient (id_patient)');
        $this->addSql('ALTER TABLE patient ADD CONSTRAINT FK_1ADAD7EB50EAE44 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur)');
        $this->addSql('ALTER TABLE etre ADD CONSTRAINT FK_5E95960250EAE44 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur)');
        $this->addSql('ALTER TABLE etre ADD CONSTRAINT FK_5E959602C0E1077A FOREIGN KEY (id_profil) REFERENCES profil (id_profil)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE greffe DROP FOREIGN KEY FK_69FCC4BB9A0E7E03');
        $this->addSql('ALTER TABLE greffe DROP FOREIGN KEY FK_69FCC4BBC4477E9B');
        $this->addSql('ALTER TABLE patient DROP FOREIGN KEY FK_1ADAD7EB50EAE44');
        $this->addSql('ALTER TABLE etre DROP FOREIGN KEY FK_5E95960250EAE44');
        $this->addSql('ALTER TABLE etre DROP FOREIGN KEY FK_5E959602C0E1077A');
        $this->addSql('DROP TABLE greffe');
        $this->addSql('DROP TABLE patient');
        $this->addSql('DROP TABLE profil');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE etre');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('DROP INDEX `PRIMARY` ON donneur');
        $this->addSql('ALTER TABLE donneur CHANGE id_donneur id VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE donneur ADD PRIMARY KEY (id)');
    }
}
