<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251117091244 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE donneur (id VARCHAR(50) NOT NULL, n_cristal VARCHAR(50) DEFAULT NULL, g_sanguin VARCHAR(50) DEFAULT NULL, sexe BOOLEAN DEFAULT NULL, age DATE DEFAULT NULL, poids VARCHAR(50) DEFAULT NULL, commentaire_patient VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE greffe (id SERIAL NOT NULL, donneur_id VARCHAR(50) NOT NULL, patient_id INT NOT NULL, date_greffe DATE DEFAULT NULL, rang_greffe INT DEFAULT NULL, type_donneur VARCHAR(50) DEFAULT NULL, type_greffe VARCHAR(50) DEFAULT NULL, greffon_fonctionnel BOOLEAN DEFAULT NULL, date_heure_fin TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, cause_fin_fonct_greffe TEXT DEFAULT NULL, date_declampage DATE DEFAULT NULL, heure_declampage TIME(0) WITHOUT TIME ZONE DEFAULT NULL, cote_prelevement_rein VARCHAR(50) DEFAULT NULL, cote_transplantation_rein VARCHAR(50) DEFAULT NULL, ischemie_total TIME(0) WITHOUT TIME ZONE DEFAULT NULL, duree_anastomoses INT DEFAULT NULL, sonde_jj BOOLEAN DEFAULT NULL, commentaire TEXT DEFAULT NULL, compte_rendu_operatoire TEXT DEFAULT NULL, protocole BOOLEAN DEFAULT NULL, dialyse BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_69FCC4BB9789825B ON greffe (donneur_id)');
        $this->addSql('CREATE INDEX IDX_69FCC4BB6B899279 ON greffe (patient_id)');
        $this->addSql('CREATE TABLE patient (id SERIAL NOT NULL, utilisateur_id VARCHAR(50) NOT NULL, ndossier VARCHAR(50) NOT NULL, nom VARCHAR(50) DEFAULT NULL, prenom VARCHAR(50) DEFAULT NULL, date_naissance DATE DEFAULT NULL, cp VARCHAR(50) DEFAULT NULL, ville_res VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1ADAD7EBFB88E14F ON patient (utilisateur_id)');
        $this->addSql('CREATE TABLE profil (id VARCHAR(50) NOT NULL, role VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE utilisateur (id VARCHAR(50) NOT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, ville_res VARCHAR(50) DEFAULT NULL, cp VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1D1C63B3E7927C74 ON utilisateur (email)');
        $this->addSql('CREATE TABLE etre (utilisateur_id VARCHAR(50) NOT NULL, profil_id VARCHAR(50) NOT NULL, PRIMARY KEY(utilisateur_id, profil_id))');
        $this->addSql('CREATE INDEX IDX_5E959602FB88E14F ON etre (utilisateur_id)');
        $this->addSql('CREATE INDEX IDX_5E959602275ED078 ON etre (profil_id)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE greffe ADD CONSTRAINT FK_69FCC4BB9789825B FOREIGN KEY (donneur_id) REFERENCES donneur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE greffe ADD CONSTRAINT FK_69FCC4BB6B899279 FOREIGN KEY (patient_id) REFERENCES patient (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE patient ADD CONSTRAINT FK_1ADAD7EBFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE etre ADD CONSTRAINT FK_5E959602FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE etre ADD CONSTRAINT FK_5E959602275ED078 FOREIGN KEY (profil_id) REFERENCES profil (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE greffe DROP CONSTRAINT FK_69FCC4BB9789825B');
        $this->addSql('ALTER TABLE greffe DROP CONSTRAINT FK_69FCC4BB6B899279');
        $this->addSql('ALTER TABLE patient DROP CONSTRAINT FK_1ADAD7EBFB88E14F');
        $this->addSql('ALTER TABLE etre DROP CONSTRAINT FK_5E959602FB88E14F');
        $this->addSql('ALTER TABLE etre DROP CONSTRAINT FK_5E959602275ED078');
        $this->addSql('DROP TABLE donneur');
        $this->addSql('DROP TABLE greffe');
        $this->addSql('DROP TABLE patient');
        $this->addSql('DROP TABLE profil');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE etre');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
