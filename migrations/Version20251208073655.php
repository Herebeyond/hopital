<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251208073655 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Complete database restructure with all required tables';
    }

    public function up(Schema $schema): void
    {
        // Drop existing tables
        $this->addSql('DROP TABLE IF EXISTS etre');
        $this->addSql('DROP TABLE IF EXISTS greffe');
        $this->addSql('DROP TABLE IF EXISTS patient');
        $this->addSql('DROP TABLE IF EXISTS utilisateur');
        $this->addSql('DROP TABLE IF EXISTS profil');
        $this->addSql('DROP TABLE IF EXISTS donneur');
        $this->addSql('DROP TABLE IF EXISTS messenger_messages');
        
        // Create base tables
        $this->addSql('CREATE TABLE Profil(
            id_profil INT AUTO_INCREMENT,
            Role VARCHAR(50) NOT NULL,
            PRIMARY KEY(id_profil)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE Donneur(
            id_donneur INT AUTO_INCREMENT,
            N_Cristal VARCHAR(50),
            G_sanguin VARCHAR(50),
            Sexe TINYINT(1),
            Age DATE,
            Poids VARCHAR(50),
            Commentaire_patient VARCHAR(50),
            PRIMARY KEY(id_donneur)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE Lien_parente(
            id_lien INT AUTO_INCREMENT,
            Lib_Lien VARCHAR(50),
            Description VARCHAR(50),
            PRIMARY KEY(id_lien)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE Voie_abord(
            id_voie INT AUTO_INCREMENT,
            Lib_voie VARCHAR(50),
            Valeur VARCHAR(50),
            PRIMARY KEY(id_voie)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE Cause_deces(
            id_deces INT AUTO_INCREMENT,
            Lib_deces VARCHAR(50),
            Description VARCHAR(50),
            PRIMARY KEY(id_deces)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE Statut_virologique(
            id_SV INT AUTO_INCREMENT,
            Libelle_SV VARCHAR(50),
            PRIMARY KEY(id_SV)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE Valeur_viriologique(
            id_val_statut INT AUTO_INCREMENT,
            Libelle_val_statut VARCHAR(50),
            PRIMARY KEY(id_val_statut)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE Incompatibilite_HLA(
            id_HLA INT AUTO_INCREMENT,
            Libelle_HLA VARCHAR(50),
            PRIMARY KEY(id_HLA)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE Valeur_I_HLA(
            id_val_HLA INT AUTO_INCREMENT,
            Libelle_val_HLA VARCHAR(50),
            PRIMARY KEY(id_val_HLA)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE Risque_immunologique(
            id_immunologique INT AUTO_INCREMENT,
            Libelle_immunologique VARCHAR(50),
            Commentaire_risque TEXT,
            PRIMARY KEY(id_immunologique)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE Conditionnement_immunosupresseur(
            id_immunosupresseur INT AUTO_INCREMENT,
            Libelle_immunosupresseur VARCHAR(50),
            Commentaire_immunosupresseur VARCHAR(50),
            PRIMARY KEY(id_immunosupresseur)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE Groupe_HLA(
            id_groupe_HLA INT AUTO_INCREMENT,
            Libelle_groupe_HLA VARCHAR(50),
            PRIMARY KEY(id_groupe_HLA)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE Groupe_serologie(
            id_serologie INT AUTO_INCREMENT,
            Libelle_serologie VARCHAR(50),
            PRIMARY KEY(id_serologie)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE Valeur_serologie(
            id_valeur_serologie INT AUTO_INCREMENT,
            Libelle_valeur_serologie VARCHAR(50),
            PRIMARY KEY(id_valeur_serologie)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Create tables with foreign keys
        $this->addSql('CREATE TABLE Valeur_groupe_HLA(
            id_valeur_HLA INT AUTO_INCREMENT,
            Libelle_valeur_HLA VARCHAR(50),
            id_donneur INT,
            id_groupe_HLA INT,
            PRIMARY KEY(id_valeur_HLA),
            INDEX IDX_VGH_DONNEUR (id_donneur),
            INDEX IDX_VGH_GROUPE (id_groupe_HLA),
            FOREIGN KEY(id_donneur) REFERENCES Donneur(id_donneur),
            FOREIGN KEY(id_groupe_HLA) REFERENCES Groupe_HLA(id_groupe_HLA)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE Utilisateur(
            id_utilisateur INT AUTO_INCREMENT,
            Nom VARCHAR(50) NOT NULL,
            Prenom VARCHAR(50) NOT NULL,
            Email VARCHAR(180) NOT NULL,
            Password VARCHAR(255) NOT NULL,
            Ville_res VARCHAR(50),
            CP VARCHAR(50),
            PRIMARY KEY(id_utilisateur),
            UNIQUE INDEX UNIQ_UTI_EMAIL (Email)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE Personnel_medical(
            id_medical INT AUTO_INCREMENT,
            Type VARCHAR(50),
            id_utilisateur INT NOT NULL,
            PRIMARY KEY(id_medical),
            UNIQUE INDEX UNIQ_PM_UTI (id_utilisateur),
            FOREIGN KEY(id_utilisateur) REFERENCES Utilisateur(id_utilisateur)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE Autre(
            id_autre INT AUTO_INCREMENT,
            id_utilisateur INT NOT NULL,
            PRIMARY KEY(id_autre),
            UNIQUE INDEX UNIQ_AUT_UTI (id_utilisateur),
            FOREIGN KEY(id_utilisateur) REFERENCES Utilisateur(id_utilisateur)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE Donneur_v(
            id_donneur_v INT AUTO_INCREMENT,
            Nom VARCHAR(50),
            Prenom VARCHAR(50),
            IMC VARCHAR(50),
            Creatinine VARCHAR(50),
            Clairance_calculee VARCHAR(50),
            Clairance_isotopique VARCHAR(50),
            Protenurie VARCHAR(50),
            Robot TINYINT(1),
            id_voie INT NOT NULL,
            id_lien INT NOT NULL,
            id_donneur INT NOT NULL,
            PRIMARY KEY(id_donneur_v),
            UNIQUE INDEX UNIQ_DV_DON (id_donneur),
            INDEX IDX_DV_VOIE (id_voie),
            INDEX IDX_DV_LIEN (id_lien),
            FOREIGN KEY(id_voie) REFERENCES Voie_abord(id_voie),
            FOREIGN KEY(id_lien) REFERENCES Lien_parente(id_lien),
            FOREIGN KEY(id_donneur) REFERENCES Donneur(id_donneur)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE Donneur_d(
            id_donneur_d INT AUTO_INCREMENT,
            Ville_origine VARCHAR(50),
            Commentaire_deces TEXT,
            Donneur_criteres_etendus TINYINT(1),
            Arret_cardiaque TINYINT(1),
            Duree_cardiaque TIME,
            PA_moyenne DECIMAL(15,2),
            Amines TINYINT(1),
            Transfusion VARCHAR(50),
            CGR TINYINT(1),
            CPA TINYINT(1),
            PFC TINYINT(1),
            Creatinine_arrive VARCHAR(50),
            Creatinine_prelevement VARCHAR(50),
            DFG DECIMAL(15,2),
            Atherome_aorte VARCHAR(50),
            Plaques_aorte TINYINT(1),
            Atherome_artere_ostium VARCHAR(50),
            Plaque_calcifiees_artere_ostium VARCHAR(50),
            Atherome_artere_renale VARCHAR(50),
            Plaque_calcifiees_artere_renale VARCHAR(50),
            Uretere VARCHAR(50),
            Plaie_digestive VARCHAR(50),
            Liquide_conservation VARCHAR(50),
            Infection_liquide_conservation VARCHAR(50),
            id_deces INT NOT NULL,
            id_donneur INT NOT NULL,
            PRIMARY KEY(id_donneur_d),
            UNIQUE INDEX UNIQ_DD_DON (id_donneur),
            INDEX IDX_DD_DECES (id_deces),
            FOREIGN KEY(id_deces) REFERENCES Cause_deces(id_deces),
            FOREIGN KEY(id_donneur) REFERENCES Donneur(id_donneur)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE Patient(
            id_patient INT AUTO_INCREMENT,
            Ndossier VARCHAR(50) NOT NULL,
            Nom VARCHAR(50),
            Prenom VARCHAR(50),
            Date_naissance DATE,
            CP VARCHAR(50),
            Ville_res VARCHAR(50),
            id_utilisateur INT NOT NULL,
            PRIMARY KEY(id_patient),
            INDEX IDX_PAT_UTI (id_utilisateur),
            FOREIGN KEY(id_utilisateur) REFERENCES Utilisateur(id_utilisateur)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE Greffe(
            id_greffon INT AUTO_INCREMENT,
            Date_greffe DATE,
            Rang_greffe INT,
            Type_donneur VARCHAR(50),
            Type_greffe VARCHAR(50),
            Greffon_fonctionnel TINYINT(1),
            Date_heure_fin DATETIME,
            Cause_fin_fonct_gref TEXT,
            Date_declampage DATE,
            Heure_declampage TIME,
            Cote_prelevement_rein VARCHAR(50),
            Cote_transplantation_rein VARCHAR(50),
            En VARCHAR(50),
            Ischemie_total TIME,
            Duree_anastomoses INT,
            Sonde_jj TINYINT(1),
            Commentaire TEXT,
            Compte_rendu_operatoire TEXT,
            Protocole TINYINT(1),
            Commentaire_protocole VARCHAR(50),
            Dialyse TINYINT(1),
            Date_derniere_dialyse VARCHAR(50),
            id_immunosupresseur INT NOT NULL,
            id_immunologique INT NOT NULL,
            id_donneur INT NOT NULL,
            id_patient INT NOT NULL,
            PRIMARY KEY(id_greffon),
            UNIQUE INDEX UNIQ_GRE_DON (id_donneur),
            INDEX IDX_GRE_IMMUNO (id_immunosupresseur),
            INDEX IDX_GRE_RISQUE (id_immunologique),
            INDEX IDX_GRE_PAT (id_patient),
            FOREIGN KEY(id_immunosupresseur) REFERENCES Conditionnement_immunosupresseur(id_immunosupresseur),
            FOREIGN KEY(id_immunologique) REFERENCES Risque_immunologique(id_immunologique),
            FOREIGN KEY(id_donneur) REFERENCES Donneur(id_donneur),
            FOREIGN KEY(id_patient) REFERENCES Patient(id_patient)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Association tables
        $this->addSql('CREATE TABLE Etre(
            id_utilisateur INT,
            id_profil INT,
            PRIMARY KEY(id_utilisateur, id_profil),
            INDEX IDX_ETR_UTI (id_utilisateur),
            INDEX IDX_ETR_PRO (id_profil),
            FOREIGN KEY(id_utilisateur) REFERENCES Utilisateur(id_utilisateur),
            FOREIGN KEY(id_profil) REFERENCES Profil(id_profil)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE Statut_Virologique_Greffe(
            id_greffon INT,
            id_SV INT,
            id_val_statut INT,
            PRIMARY KEY(id_greffon, id_SV, id_val_statut),
            INDEX IDX_SVG_GRE (id_greffon),
            INDEX IDX_SVG_SV (id_SV),
            INDEX IDX_SVG_VAL (id_val_statut),
            FOREIGN KEY(id_greffon) REFERENCES Greffe(id_greffon),
            FOREIGN KEY(id_SV) REFERENCES Statut_virologique(id_SV),
            FOREIGN KEY(id_val_statut) REFERENCES Valeur_viriologique(id_val_statut)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE Incompatibilite_HLA_Greffe(
            id_greffon INT,
            id_HLA INT,
            id_val_HLA INT,
            PRIMARY KEY(id_greffon, id_HLA, id_val_HLA),
            INDEX IDX_IHG_GRE (id_greffon),
            INDEX IDX_IHG_HLA (id_HLA),
            INDEX IDX_IHG_VAL (id_val_HLA),
            FOREIGN KEY(id_greffon) REFERENCES Greffe(id_greffon),
            FOREIGN KEY(id_HLA) REFERENCES Incompatibilite_HLA(id_HLA),
            FOREIGN KEY(id_val_HLA) REFERENCES Valeur_I_HLA(id_val_HLA)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE Serologie_Donneur(
            id_donneur INT,
            id_serologie INT,
            id_valeur_serologie INT,
            PRIMARY KEY(id_donneur, id_serologie, id_valeur_serologie),
            INDEX IDX_SD_DON (id_donneur),
            INDEX IDX_SD_SER (id_serologie),
            INDEX IDX_SD_VAL (id_valeur_serologie),
            FOREIGN KEY(id_donneur) REFERENCES Donneur(id_donneur),
            FOREIGN KEY(id_serologie) REFERENCES Groupe_serologie(id_serologie),
            FOREIGN KEY(id_valeur_serologie) REFERENCES Valeur_serologie(id_valeur_serologie)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Recreate messenger_messages table
        $this->addSql('CREATE TABLE messenger_messages (
            id BIGINT AUTO_INCREMENT NOT NULL,
            body LONGTEXT NOT NULL,
            headers LONGTEXT NOT NULL,
            queue_name VARCHAR(190) NOT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            INDEX IDX_75EA56E0FB7336F0 (queue_name),
            INDEX IDX_75EA56E0E3BD61CE (available_at),
            INDEX IDX_75EA56E016BA31DB (delivered_at),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // Drop all tables in reverse order
        $this->addSql('DROP TABLE IF EXISTS messenger_messages');
        $this->addSql('DROP TABLE IF EXISTS Serologie_Donneur');
        $this->addSql('DROP TABLE IF EXISTS Incompatibilite_HLA_Greffe');
        $this->addSql('DROP TABLE IF EXISTS Statut_Virologique_Greffe');
        $this->addSql('DROP TABLE IF EXISTS Etre');
        $this->addSql('DROP TABLE IF EXISTS Greffe');
        $this->addSql('DROP TABLE IF EXISTS Patient');
        $this->addSql('DROP TABLE IF EXISTS Donneur_d');
        $this->addSql('DROP TABLE IF EXISTS Donneur_v');
        $this->addSql('DROP TABLE IF EXISTS Autre');
        $this->addSql('DROP TABLE IF EXISTS Personnel_medical');
        $this->addSql('DROP TABLE IF EXISTS Utilisateur');
        $this->addSql('DROP TABLE IF EXISTS Valeur_groupe_HLA');
        $this->addSql('DROP TABLE IF EXISTS Valeur_serologie');
        $this->addSql('DROP TABLE IF EXISTS Groupe_serologie');
        $this->addSql('DROP TABLE IF EXISTS Groupe_HLA');
        $this->addSql('DROP TABLE IF EXISTS Conditionnement_immunosupresseur');
        $this->addSql('DROP TABLE IF EXISTS Risque_immunologique');
        $this->addSql('DROP TABLE IF EXISTS Valeur_I_HLA');
        $this->addSql('DROP TABLE IF EXISTS Incompatibilite_HLA');
        $this->addSql('DROP TABLE IF EXISTS Valeur_viriologique');
        $this->addSql('DROP TABLE IF EXISTS Statut_virologique');
        $this->addSql('DROP TABLE IF EXISTS Cause_deces');
        $this->addSql('DROP TABLE IF EXISTS Voie_abord');
        $this->addSql('DROP TABLE IF EXISTS Lien_parente');
        $this->addSql('DROP TABLE IF EXISTS Donneur');
        $this->addSql('DROP TABLE IF EXISTS Profil');
    }
}
