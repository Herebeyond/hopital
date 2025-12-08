-- ============================================
-- Script de création de la base de données
-- Système: PostgreSQL
-- Version: 1.0
-- ============================================

-- Supprimer les triggers existants
DROP TRIGGER IF EXISTS check_donneur_type_insert_v ON Donneur_v;
DROP TRIGGER IF EXISTS check_donneur_type_insert_d ON Donneur_d;
DROP TRIGGER IF EXISTS check_greffe_fin_fonctionnement ON Greffe;
DROP TRIGGER IF EXISTS check_greffe_fin_fonctionnement_update ON Greffe;
DROP TRIGGER IF EXISTS prevent_patient_deletion ON Patient;

-- Supprimer les fonctions de trigger
DROP FUNCTION IF EXISTS check_donneur_type_v();
DROP FUNCTION IF EXISTS check_donneur_type_d();
DROP FUNCTION IF EXISTS check_greffe_fin_fonctionnement_func();
DROP FUNCTION IF EXISTS prevent_patient_deletion_func();

-- Supprimer les tables dans l'ordre des dépendances
DROP TABLE IF EXISTS Donneur_Serologie CASCADE;
DROP TABLE IF EXISTS Greffe_Incompatibilite_HLA CASCADE;
DROP TABLE IF EXISTS Greffe_Statut_Virologique CASCADE;
DROP TABLE IF EXISTS Participer CASCADE;
DROP TABLE IF EXISTS Etre CASCADE;
DROP TABLE IF EXISTS Greffe CASCADE;
DROP TABLE IF EXISTS Patient CASCADE;
DROP TABLE IF EXISTS Donneur_d CASCADE;
DROP TABLE IF EXISTS Donneur_v CASCADE;
DROP TABLE IF EXISTS Autre CASCADE;
DROP TABLE IF EXISTS Personnel_medical CASCADE;
DROP TABLE IF EXISTS Utilisateur CASCADE;
DROP TABLE IF EXISTS Valeur_serologie CASCADE;
DROP TABLE IF EXISTS Groupe_serologie CASCADE;
DROP TABLE IF EXISTS Valeur_groupe_HLA CASCADE;
DROP TABLE IF EXISTS Groupe_HLA CASCADE;
DROP TABLE IF EXISTS Conditionnement_immunosupresseur CASCADE;
DROP TABLE IF EXISTS Risque_immunologique CASCADE;
DROP TABLE IF EXISTS Valeur_I_HLA CASCADE;
DROP TABLE IF EXISTS Incompatibilite_HLA CASCADE;
DROP TABLE IF EXISTS Valeur_viriologique CASCADE;
DROP TABLE IF EXISTS Statut_virologique CASCADE;
DROP TABLE IF EXISTS Cause_deces CASCADE;
DROP TABLE IF EXISTS Voie_abord CASCADE;
DROP TABLE IF EXISTS Lien_parente CASCADE;
DROP TABLE IF EXISTS Donneur CASCADE;
DROP TABLE IF EXISTS Personnel_ope CASCADE;
DROP TABLE IF EXISTS Profil CASCADE;

-- ============================================
-- Table des profils utilisateurs (rôles système)
-- ============================================
CREATE TABLE Profil(
   id_profil VARCHAR(50),
   Role VARCHAR(50) NOT NULL,
   PRIMARY KEY(id_profil)
);

-- ============================================
-- Personnel opératoire participant aux greffes
-- ============================================
CREATE TABLE Personnel_ope(
   id_personnel_op VARCHAR(50),
   Nom VARCHAR(50),
   Poste VARCHAR(50),
   PRIMARY KEY(id_personnel_op)
);

-- ============================================
-- Donneur (vivant ou décédé) - table principale
-- ============================================
CREATE TABLE Donneur(
   id_donneur VARCHAR(50),
   N_Cristal VARCHAR(50),  -- Numéro d'identification Cristal
   G_sanguin VARCHAR(50) CHECK(G_sanguin IN ('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') OR G_sanguin IS NULL),  -- Groupe sanguin
   Sexe BOOLEAN CHECK(Sexe IN (FALSE, TRUE) OR Sexe IS NULL),  -- FALSE=Femme, TRUE=Homme
   Age DATE,
   Poids VARCHAR(50),
   Commentaire_patient VARCHAR(50),
   PRIMARY KEY(id_donneur)
);

-- ============================================
-- Lien de parenté (pour donneurs vivants)
-- ============================================
CREATE TABLE Lien_parente(
   id_lien VARCHAR(50),
   Lib_Lien VARCHAR(50),  -- Ex: Parent, Enfant, Conjoint, Frère/Sœur
   Description VARCHAR(50),
   PRIMARY KEY(id_lien)
);

-- ============================================
-- Voie d'abord chirurgicale
-- ============================================
CREATE TABLE Voie_abord(
   id_voie VARCHAR(50),
   Lib_voie VARCHAR(50),  -- Ex: Lombotomie, Coelioscopie
   Valeur VARCHAR(50),
   PRIMARY KEY(id_voie)
);

-- ============================================
-- Cause de décès (pour donneurs décédés)
-- ============================================
CREATE TABLE Cause_deces(
   id_deces VARCHAR(50),
   Lib_deces VARCHAR(50),  -- Ex: AVC, Traumatisme crânien
   Description VARCHAR(50),
   PRIMARY KEY(id_deces)
);

-- ============================================
-- Statuts virologiques (CMV, EBV, VHB, VHC, VIH)
-- ============================================
CREATE TABLE Statut_virologique(
   id_SV VARCHAR(50),
   Libelle_SV VARCHAR(50),  -- Ex: CMV, EBV, VHB, VHC, VIH
   PRIMARY KEY(id_SV)
);

-- ============================================
-- Valeurs virologiques (Positif/Négatif)
-- ============================================
CREATE TABLE Valeur_viriologique(
   id_val_statut VARCHAR(50),
   Libelle_val_statut VARCHAR(50),  -- Ex: Positif, Négatif, Indéterminé
   PRIMARY KEY(id_val_statut)
);

-- ============================================
-- Types d'incompatibilités HLA
-- ============================================
CREATE TABLE Incompatibilite_HLA(
   id_HLA VARCHAR(50),
   Libelle_HLA VARCHAR(50),  -- Ex: HLA-A, HLA-B, HLA-DR
   PRIMARY KEY(id_HLA)
);

-- ============================================
-- Valeurs d'incompatibilités HLA
-- ============================================
CREATE TABLE Valeur_I_HLA(
   id_val_HLA VARCHAR(50),
   Libelle_val_HLA VARCHAR(50),  -- Ex: Compatible, Incompatible
   PRIMARY KEY(id_val_HLA)
);

-- ============================================
-- Risques immunologiques du receveur
-- ============================================
CREATE TABLE Risque_immunologique(
   id_immunologique VARCHAR(50),
   Libelle_immunologique VARCHAR(50),  -- Ex: Standard, Élevé, Hyperimmunisé
   Commentaire_risque TEXT,
   PRIMARY KEY(id_immunologique)
);

-- ============================================
-- Protocoles d'immunosuppression
-- ============================================
CREATE TABLE Conditionnement_immunosupresseur(
   id_immunosupresseur VARCHAR(50),
   Libelle_immunosupresseur VARCHAR(50),  -- Ex: Standard, Renforcé, Thymoglobuline
   Commentaire_immunosupresseur VARCHAR(50),
   PRIMARY KEY(id_immunosupresseur)
);

-- ============================================
-- Groupes HLA (A, B, DR)
-- ============================================
CREATE TABLE Groupe_HLA(
   id_groupe_HLA VARCHAR(50),
   Libelle_groupe_HLA VARCHAR(50),  -- Ex: HLA-A, HLA-B, HLA-DR
   PRIMARY KEY(id_groupe_HLA)
);

-- ============================================
-- Valeurs de typage HLA du donneur
-- ============================================
CREATE TABLE Valeur_groupe_HLA(
   id_valeur_HLA VARCHAR(50),
   Libelle_valeur_HLA VARCHAR(50),  -- Ex: A1, A2, B7, DR15
   id_donneur VARCHAR(50),
   id_groupe_HLA VARCHAR(50),
   PRIMARY KEY(id_valeur_HLA),
   FOREIGN KEY(id_donneur) REFERENCES Donneur(id_donneur) ON DELETE CASCADE ON UPDATE CASCADE,
   FOREIGN KEY(id_groupe_HLA) REFERENCES Groupe_HLA(id_groupe_HLA) ON UPDATE CASCADE
);

-- ============================================
-- Groupes de sérologies (CMV, EBV, etc.)
-- ============================================
CREATE TABLE Groupe_serologie(
   id_serologie VARCHAR(50),
   Libelle_serologie VARCHAR(50),  -- Ex: CMV, EBV, Toxoplasmose
   PRIMARY KEY(id_serologie)
);

-- ============================================
-- Valeurs de sérologies (Positif/Négatif)
-- ============================================
CREATE TABLE Valeur_serologie(
   id_valeur_serologie VARCHAR(50),
   Libelle_valeur_serologie VARCHAR(50),  -- Ex: IgG+, IgM+, Négatif
   PRIMARY KEY(id_valeur_serologie)
);

-- ============================================
-- Utilisateurs du système
-- ============================================
CREATE TABLE Utilisateur(
   id_utilisateur VARCHAR(50),
   Nom VARCHAR(50),
   Prenom VARCHAR(50),
   Ville_res VARCHAR(50),
   CP VARCHAR(50),
   id_personnel_op VARCHAR(50),
   email VARCHAR(180) UNIQUE,  -- Email pour authentification
   password VARCHAR(255),  -- Mot de passe hashé
   PRIMARY KEY(id_utilisateur),
   UNIQUE(id_personnel_op),
   FOREIGN KEY(id_personnel_op) REFERENCES Personnel_ope(id_personnel_op) ON UPDATE CASCADE
);

-- ============================================
-- Personnel médical (spécialisation utilisateur)
-- ============================================
CREATE TABLE Personnel_medical(
   id_medical VARCHAR(50),
   Type VARCHAR(50),  -- Ex: Chirurgien, Néphrologue, Anesthésiste
   id_utilisateur VARCHAR(50) NOT NULL,
   PRIMARY KEY(id_medical),
   UNIQUE(id_utilisateur),
   FOREIGN KEY(id_utilisateur) REFERENCES Utilisateur(id_utilisateur) ON DELETE CASCADE ON UPDATE CASCADE
);

-- ============================================
-- Autre personnel (spécialisation utilisateur)
-- ============================================
CREATE TABLE Autre(
   id_autre VARCHAR(50),
   id_utilisateur VARCHAR(50) NOT NULL,
   PRIMARY KEY(id_autre),
   UNIQUE(id_utilisateur),
   FOREIGN KEY(id_utilisateur) REFERENCES Utilisateur(id_utilisateur) ON DELETE CASCADE ON UPDATE CASCADE
);

-- ============================================
-- Donneur vivant (informations spécifiques)
-- ============================================
CREATE TABLE Donneur_v(
   id_donneur_v VARCHAR(50),
   Nom VARCHAR(50),
   Prenom VARCHAR(50),
   IMC VARCHAR(50),
   Creatinine VARCHAR(50),
   Clairance_calculee VARCHAR(50),
   Clairance_isotopique VARCHAR(50),
   Proténurie VARCHAR(50),
   Robot BOOLEAN CHECK(Robot IN (FALSE, TRUE) OR Robot IS NULL),  -- FALSE=Non, TRUE=Oui (chirurgie robotique)
   id_voie VARCHAR(50) NOT NULL,
   id_lien VARCHAR(50) NOT NULL,
   id_donneur VARCHAR(50) NOT NULL,
   PRIMARY KEY(id_donneur_v),
   UNIQUE(id_donneur),
   FOREIGN KEY(id_voie) REFERENCES Voie_abord(id_voie) ON UPDATE CASCADE,
   FOREIGN KEY(id_lien) REFERENCES Lien_parente(id_lien) ON UPDATE CASCADE,
   FOREIGN KEY(id_donneur) REFERENCES Donneur(id_donneur) ON DELETE CASCADE ON UPDATE CASCADE
);

-- ============================================
-- Donneur décédé (informations spécifiques)
-- ============================================
CREATE TABLE Donneur_d(
   id_donneur_d VARCHAR(50),
   Ville_origine VARCHAR(50),
   Commentaire_deces TEXT,
   Donneur_criteres_etendus BOOLEAN CHECK(Donneur_criteres_etendus IN (FALSE, TRUE) OR Donneur_criteres_etendus IS NULL),  -- Critères élargis
   Arret_cardiaque BOOLEAN CHECK(Arret_cardiaque IN (FALSE, TRUE) OR Arret_cardiaque IS NULL),
   Duree_cardiaque TIME CHECK(Duree_cardiaque >= '00:00:00' OR Duree_cardiaque IS NULL),
   PA_moyenne DECIMAL(15,2) CHECK(PA_moyenne >= 0 OR PA_moyenne IS NULL),  -- Pression artérielle moyenne
   Amines BOOLEAN CHECK(Amines IN (FALSE, TRUE) OR Amines IS NULL),  -- Support par amines vasopressives
   Transfusion VARCHAR(50),
   CGR BOOLEAN CHECK(CGR IN (FALSE, TRUE) OR CGR IS NULL),  -- Concentré de globules rouges
   CPA BOOLEAN CHECK(CPA IN (FALSE, TRUE) OR CPA IS NULL),  -- Concentré plaquettaire d'aphérèse
   PFC BOOLEAN CHECK(PFC IN (FALSE, TRUE) OR PFC IS NULL),  -- Plasma frais congelé
   Creatinine_arrive VARCHAR(50),
   Creatinine_prelevement VARCHAR(50),
   DFG DECIMAL(15,2) CHECK(DFG >= 0 OR DFG IS NULL),  -- Débit de filtration glomérulaire
   Atherome_aorte VARCHAR(50),
   Plaques_aorte BOOLEAN CHECK(Plaques_aorte IN (FALSE, TRUE) OR Plaques_aorte IS NULL),
   Atherome_artere_ostium VARCHAR(50),
   Plaque_calcifiées_artere_ostium VARCHAR(50),
   Atherome_artére_renale VARCHAR(50),
   Plaque_calcifiées_artere_renale VARCHAR(50),
   Uretere VARCHAR(50),
   Plaie_digestive VARCHAR(50),
   Liquide_conservation VARCHAR(50),
   Infection_liquide_conservation VARCHAR(50),
   id_deces VARCHAR(50) NOT NULL,
   id_donneur VARCHAR(50) NOT NULL,
   PRIMARY KEY(id_donneur_d),
   UNIQUE(id_donneur),
   FOREIGN KEY(id_deces) REFERENCES Cause_deces(id_deces) ON UPDATE CASCADE,
   FOREIGN KEY(id_donneur) REFERENCES Donneur(id_donneur) ON DELETE CASCADE ON UPDATE CASCADE
);

-- ============================================
-- Patients receveurs de greffe
-- ============================================
CREATE TABLE Patient(
   id_patient SERIAL,
   Ndossier VARCHAR(50) NOT NULL,  -- Numéro de dossier hospitalier
   Nom VARCHAR(50),
   Prenom VARCHAR(50),
   Date_naissance DATE CHECK(Date_naissance <= CURRENT_DATE OR Date_naissance IS NULL),
   CP VARCHAR(50),
   Ville_res VARCHAR(50),
   id_utilisateur VARCHAR(50) NOT NULL,
   PRIMARY KEY(id_patient),
   FOREIGN KEY(id_utilisateur) REFERENCES Utilisateur(id_utilisateur) ON DELETE RESTRICT ON UPDATE CASCADE
);

-- ============================================
-- Greffe rénale (intervention chirurgicale)
-- ============================================
CREATE TABLE Greffe(
   id_greffon SERIAL,
   Date_greffe DATE CHECK(Date_greffe <= CURRENT_DATE OR Date_greffe IS NULL),
   Rang_greffe INT CHECK(Rang_greffe >= 1 OR Rang_greffe IS NULL),  -- 1=première greffe, 2=deuxième, etc.
   Type_donneur VARCHAR(50) CHECK(Type_donneur IN ('vivant', 'décédé') OR Type_donneur IS NULL),
   Type_greffe VARCHAR(50),
   Greffon_fonctionnel BOOLEAN CHECK(Greffon_fonctionnel IN (FALSE, TRUE) OR Greffon_fonctionnel IS NULL),  -- FALSE=Non, TRUE=Oui
   Date_heure_fin TIMESTAMP,
   Cause_fin_fonct_gref TEXT,
   Date_declampage DATE,
   Heure_declampage TIME,
   Cote_prelevement_rein VARCHAR(50) CHECK(Cote_prelevement_rein IN ('Gauche', 'Droit') OR Cote_prelevement_rein IS NULL),
   Cote_transplantation_rein VARCHAR(50) CHECK(Cote_transplantation_rein IN ('Gauche', 'Droit') OR Cote_transplantation_rein IS NULL),
   En VARCHAR(50),
   Ischémie_total TIME CHECK(Ischémie_total >= '00:00:00' OR Ischémie_total IS NULL),  -- Durée totale d'ischémie
   Duree_anastomoses INT CHECK(Duree_anastomoses >= 0 OR Duree_anastomoses IS NULL),  -- Durée en minutes
   Sonde_jj BOOLEAN CHECK(Sonde_jj IN (FALSE, TRUE) OR Sonde_jj IS NULL),  -- Sonde double J
   Commentaire TEXT,
   Compte_rendu_operatoire TEXT,
   Protocole BOOLEAN CHECK(Protocole IN (FALSE, TRUE) OR Protocole IS NULL),  -- Inclusion dans un protocole de recherche
   Commentaire_protocole VARCHAR(50),
   Dialyse BOOLEAN CHECK(Dialyse IN (FALSE, TRUE) OR Dialyse IS NULL),  -- Patient dialysé avant greffe
   Date_derniere_dialyse VARCHAR(50),
   id_immunosupresseur VARCHAR(50) NOT NULL,
   id_immunologique VARCHAR(50) NOT NULL,
   id_donneur VARCHAR(50) NOT NULL,
   id_patient INT NOT NULL,
   PRIMARY KEY(id_greffon),
   UNIQUE(id_donneur),
   FOREIGN KEY(id_immunosupresseur) REFERENCES Conditionnement_immunosupresseur(id_immunosupresseur) ON UPDATE CASCADE,
   FOREIGN KEY(id_immunologique) REFERENCES Risque_immunologique(id_immunologique) ON UPDATE CASCADE,
   FOREIGN KEY(id_donneur) REFERENCES Donneur(id_donneur) ON DELETE RESTRICT ON UPDATE CASCADE,
   FOREIGN KEY(id_patient) REFERENCES Patient(id_patient) ON DELETE RESTRICT ON UPDATE CASCADE
);

-- ============================================
-- Table de jonction: Utilisateur - Profil
-- Permet d'attribuer plusieurs rôles à un utilisateur
-- ============================================
CREATE TABLE Etre(
   id_utilisateur VARCHAR(50),
   id_profil VARCHAR(50),
   PRIMARY KEY(id_utilisateur, id_profil),
   FOREIGN KEY(id_utilisateur) REFERENCES Utilisateur(id_utilisateur) ON DELETE CASCADE ON UPDATE CASCADE,
   FOREIGN KEY(id_profil) REFERENCES Profil(id_profil) ON DELETE CASCADE ON UPDATE CASCADE
);

-- ============================================
-- Table de jonction: Greffe - Personnel opératoire
-- Personnel participant à l'intervention
-- ============================================
CREATE TABLE Participer(
   id_greffon INT,
   id_personnel_op VARCHAR(50),
   PRIMARY KEY(id_greffon, id_personnel_op),
   FOREIGN KEY(id_greffon) REFERENCES Greffe(id_greffon) ON DELETE CASCADE ON UPDATE CASCADE,
   FOREIGN KEY(id_personnel_op) REFERENCES Personnel_ope(id_personnel_op) ON UPDATE CASCADE
);

-- ============================================
-- Table de jonction: Greffe - Statut virologique
-- Statuts virologiques de la greffe (CMV, EBV, etc.)
-- ============================================
CREATE TABLE Greffe_Statut_Virologique(
   id_greffon INT,
   id_SV VARCHAR(50),
   id_val_statut VARCHAR(50),
   PRIMARY KEY(id_greffon, id_SV, id_val_statut),
   FOREIGN KEY(id_greffon) REFERENCES Greffe(id_greffon) ON DELETE CASCADE ON UPDATE CASCADE,
   FOREIGN KEY(id_SV) REFERENCES Statut_virologique(id_SV) ON UPDATE CASCADE,
   FOREIGN KEY(id_val_statut) REFERENCES Valeur_viriologique(id_val_statut) ON UPDATE CASCADE
);

-- ============================================
-- Table de jonction: Greffe - Incompatibilité HLA
-- Incompatibilités HLA entre donneur et receveur
-- ============================================
CREATE TABLE Greffe_Incompatibilite_HLA(
   id_greffon INT,
   id_HLA VARCHAR(50),
   id_val_HLA VARCHAR(50),
   PRIMARY KEY(id_greffon, id_HLA, id_val_HLA),
   FOREIGN KEY(id_greffon) REFERENCES Greffe(id_greffon) ON DELETE CASCADE ON UPDATE CASCADE,
   FOREIGN KEY(id_HLA) REFERENCES Incompatibilite_HLA(id_HLA) ON UPDATE CASCADE,
   FOREIGN KEY(id_val_HLA) REFERENCES Valeur_I_HLA(id_val_HLA) ON UPDATE CASCADE
);

-- ============================================
-- Table de jonction: Donneur - Sérologie
-- Résultats sérologiques du donneur
-- ============================================
CREATE TABLE Donneur_Serologie(
   id_donneur VARCHAR(50),
   id_serologie VARCHAR(50),
   id_valeur_serologie VARCHAR(50),
   PRIMARY KEY(id_donneur, id_serologie, id_valeur_serologie),
   FOREIGN KEY(id_donneur) REFERENCES Donneur(id_donneur) ON DELETE CASCADE ON UPDATE CASCADE,
   FOREIGN KEY(id_serologie) REFERENCES Groupe_serologie(id_serologie) ON UPDATE CASCADE,
   FOREIGN KEY(id_valeur_serologie) REFERENCES Valeur_serologie(id_valeur_serologie) ON UPDATE CASCADE
);

-- ============================================
-- TRIGGERS
-- ============================================

-- Fonction trigger: Empêcher qu'un donneur soit à la fois vivant ET décédé
CREATE OR REPLACE FUNCTION check_donneur_type_v()
RETURNS TRIGGER AS $$
BEGIN
    IF EXISTS (SELECT 1 FROM Donneur_d WHERE id_donneur = NEW.id_donneur) THEN
        RAISE EXCEPTION 'Un donneur ne peut pas être à la fois vivant et décédé';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION check_donneur_type_d()
RETURNS TRIGGER AS $$
BEGIN
    IF EXISTS (SELECT 1 FROM Donneur_v WHERE id_donneur = NEW.id_donneur) THEN
        RAISE EXCEPTION 'Un donneur ne peut pas être à la fois vivant et décédé';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER check_donneur_type_insert_v
BEFORE INSERT ON Donneur_v
FOR EACH ROW
EXECUTE FUNCTION check_donneur_type_v();

CREATE TRIGGER check_donneur_type_insert_d
BEFORE INSERT ON Donneur_d
FOR EACH ROW
EXECUTE FUNCTION check_donneur_type_d();

-- Fonction trigger: Vérifier que Date_heure_fin et Cause_fin_fonct_gref sont renseignés si Greffon_fonctionnel = FALSE
CREATE OR REPLACE FUNCTION check_greffe_fin_fonctionnement_func()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.Greffon_fonctionnel = FALSE AND (NEW.Date_heure_fin IS NULL OR NEW.Cause_fin_fonct_gref IS NULL) THEN
        RAISE EXCEPTION 'Date et cause de fin doivent être renseignées si le greffon n''est plus fonctionnel';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER check_greffe_fin_fonctionnement
BEFORE INSERT ON Greffe
FOR EACH ROW
EXECUTE FUNCTION check_greffe_fin_fonctionnement_func();

CREATE TRIGGER check_greffe_fin_fonctionnement_update
BEFORE UPDATE ON Greffe
FOR EACH ROW
EXECUTE FUNCTION check_greffe_fin_fonctionnement_func();

-- Fonction trigger: Empêcher la suppression d'un patient avec des greffes associées
CREATE OR REPLACE FUNCTION prevent_patient_deletion_func()
RETURNS TRIGGER AS $$
BEGIN
    IF EXISTS (SELECT 1 FROM Greffe WHERE id_patient = OLD.id_patient) THEN
        RAISE EXCEPTION 'Impossible de supprimer un patient ayant des greffes associées';
    END IF;
    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER prevent_patient_deletion
BEFORE DELETE ON Patient
FOR EACH ROW
EXECUTE FUNCTION prevent_patient_deletion_func();

-- ============================================
-- Commentaires sur les tables
-- ============================================
COMMENT ON TABLE Profil IS 'Rôles système des utilisateurs';
COMMENT ON TABLE Personnel_ope IS 'Personnel opératoire participant aux interventions';
COMMENT ON TABLE Donneur IS 'Table principale des donneurs (vivants ou décédés)';
COMMENT ON TABLE Lien_parente IS 'Liens de parenté entre donneur vivant et receveur';
COMMENT ON TABLE Voie_abord IS 'Voies d''abord chirurgicales';
COMMENT ON TABLE Cause_deces IS 'Causes de décès des donneurs décédés';
COMMENT ON TABLE Statut_virologique IS 'Types de statuts virologiques (CMV, EBV, VHB, etc.)';
COMMENT ON TABLE Valeur_viriologique IS 'Valeurs des tests virologiques';
COMMENT ON TABLE Incompatibilite_HLA IS 'Types d''incompatibilités HLA';
COMMENT ON TABLE Valeur_I_HLA IS 'Valeurs d''incompatibilités HLA';
COMMENT ON TABLE Risque_immunologique IS 'Niveaux de risque immunologique';
COMMENT ON TABLE Conditionnement_immunosupresseur IS 'Protocoles d''immunosuppression';
COMMENT ON TABLE Groupe_HLA IS 'Groupes de typage HLA';
COMMENT ON TABLE Valeur_groupe_HLA IS 'Valeurs de typage HLA du donneur';
COMMENT ON TABLE Groupe_serologie IS 'Types de tests sérologiques';
COMMENT ON TABLE Valeur_serologie IS 'Résultats des tests sérologiques';
COMMENT ON TABLE Utilisateur IS 'Utilisateurs du système';
COMMENT ON TABLE Personnel_medical IS 'Personnel médical (spécialisation)';
COMMENT ON TABLE Autre IS 'Autre personnel (spécialisation)';
COMMENT ON TABLE Donneur_v IS 'Informations spécifiques aux donneurs vivants';
COMMENT ON TABLE Donneur_d IS 'Informations spécifiques aux donneurs décédés';
COMMENT ON TABLE Patient IS 'Patients receveurs de greffe';
COMMENT ON TABLE Greffe IS 'Interventions de greffe rénale';
COMMENT ON TABLE Etre IS 'Association utilisateur-profil (plusieurs rôles possibles)';
COMMENT ON TABLE Participer IS 'Personnel opératoire participant à une greffe';
COMMENT ON TABLE Greffe_Statut_Virologique IS 'Statuts virologiques d''une greffe';
COMMENT ON TABLE Greffe_Incompatibilite_HLA IS 'Incompatibilités HLA d''une greffe';
COMMENT ON TABLE Donneur_Serologie IS 'Résultats sérologiques d''un donneur';
