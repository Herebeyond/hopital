<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration pour renommer la table Profil en Role et id_profil en id_role
 */
final class Version20251215000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Renommer la table Profil en Role et id_profil en id_role';
    }

    public function up(Schema $schema): void
    {
        // Pour MySQL, désactiver les contraintes de clé étrangère
        $this->addSql('SET FOREIGN_KEY_CHECKS=0');
        
        // Supprimer d'abord la contrainte de clé étrangère dans Etre
        $this->addSql('ALTER TABLE Etre DROP FOREIGN KEY Etre_ibfk_2');
        
        // Renommer la colonne id_profil en id_role dans la table Profil
        $this->addSql('ALTER TABLE Profil CHANGE COLUMN id_profil id_role VARCHAR(50) NOT NULL');

        // Renommer la table Profil en Role
        $this->addSql('ALTER TABLE Profil RENAME TO Role');

        // Renommer la colonne id_profil en id_role dans la table Etre
        $this->addSql('ALTER TABLE Etre CHANGE COLUMN id_profil id_role VARCHAR(50) NOT NULL');

        // Ajouter la contrainte de clé étrangère dans Etre vers Role
        $this->addSql('ALTER TABLE Etre ADD CONSTRAINT Etre_ibfk_2 FOREIGN KEY (id_role) REFERENCES Role (id_role) ON DELETE CASCADE ON UPDATE CASCADE');
        
        $this->addSql('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(Schema $schema): void
    {
        // Pour MySQL, désactiver les contraintes de clé étrangère
        $this->addSql('SET FOREIGN_KEY_CHECKS=0');

        // Supprimer la contrainte de clé étrangère dans Etre
        $this->addSql('ALTER TABLE Etre DROP FOREIGN KEY Etre_ibfk_2');

        // Renommer la colonne id_role en id_profil dans la table Role
        $this->addSql('ALTER TABLE Role CHANGE COLUMN id_role id_profil VARCHAR(50) NOT NULL');

        // Renommer la table Role en Profil
        $this->addSql('ALTER TABLE Role RENAME TO Profil');

        // Renommer la colonne id_role en id_profil dans la table Etre
        $this->addSql('ALTER TABLE Etre CHANGE COLUMN id_role id_profil VARCHAR(50) NOT NULL');

        // Ajouter la contrainte de clé étrangère dans Etre vers Profil
        $this->addSql('ALTER TABLE Etre ADD CONSTRAINT Etre_ibfk_2 FOREIGN KEY (id_profil) REFERENCES Profil (id_profil) ON DELETE CASCADE ON UPDATE CASCADE');

        $this->addSql('SET FOREIGN_KEY_CHECKS=1');
    }
}
