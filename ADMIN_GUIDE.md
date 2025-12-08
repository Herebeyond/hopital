# Guide d'Utilisation - Interface d'Administration

## Accès à l'Interface

**URL:** http://localhost/admin

**Accès requis:** Utilisateur connecté (ROLE_USER)

---

## Structure de l'Interface

L'interface d'administration est organisée en plusieurs sections :

### 📊 Tableau de Bord
- Vue d'ensemble avec accès rapide aux principales sections
- Cartes cliquables pour chaque module
- Informations sur l'état de la base de données

### 👥 Gestion des Patients
**URL:** `/admin/patient`

**Fonctionnalités:**
- ✅ Liste complète des patients avec filtres
- ✅ Recherche par N° dossier, nom, prénom
- ✅ Ajout de nouveaux patients
- ✅ Modification des informations patient
- ✅ Vue détaillée avec liste des greffes associées
- ✅ Suppression (contrôlée par trigger)

**Champs disponibles:**
- N° Dossier (obligatoire)
- Nom, Prénom
- Date de naissance (vérifiée <= date actuelle)
- Code Postal, Ville
- Référent médical (obligatoire)

### 🫀 Gestion des Donneurs
**URL:** `/admin/donneur`

**Fonctionnalités:**
- ✅ Liste des donneurs (vivants et décédés)
- ✅ Filtres par groupe sanguin, sexe
- ✅ Ajout de nouveaux donneurs
- ✅ Modification des informations
- ✅ Vue détaillée avec greffe associée

**Champs disponibles:**
- ID Donneur (unique)
- N° Cristal
- Groupe sanguin (A+, A-, B+, B-, AB+, AB-, O+, O-)
- Sexe (Homme/Femme)
- Date de naissance
- Poids
- Commentaires

**Validation automatique:**
- ✅ Groupe sanguin validé
- ✅ Un donneur ne peut pas être à la fois vivant ET décédé (trigger)

### ❤️ Gestion des Greffes
**URL:** `/admin/greffe`

**Fonctionnalités:**
- ✅ Liste des interventions avec filtres avancés
- ✅ Recherche par patient, donneur, date
- ✅ Création de nouvelles greffes
- ✅ Modification complète
- ✅ Vue détaillée avec tous les paramètres

**Champs principaux:**
- Patient (obligatoire, autocomplete)
- Donneur (obligatoire, autocomplete)
- Date de greffe (validée <= date actuelle)
- Rang de greffe (1, 2, 3...)
- Type de donneur (vivant/décédé)
- État du greffon (fonctionnel/non fonctionnel)

**Détails chirurgicaux:**
- Date/heure de déclampage
- Côté prélèvement/transplantation (Gauche/Droit)
- Durée d'ischémie totale
- Durée des anastomoses (minutes)
- Sonde JJ (Oui/Non)

**Protocole:**
- Protocole de recherche (Oui/Non)
- Dialyse pré-greffe
- Commentaires libres
- Compte rendu opératoire complet

**Validation automatique:**
- ✅ Si greffon non fonctionnel → date/heure fin + cause obligatoires (trigger)
- ✅ Date de greffe <= date actuelle
- ✅ Rang de greffe >= 1
- ✅ Un donneur = une seule greffe (contrainte UNIQUE)

### 👤 Gestion des Utilisateurs
**URL:** `/admin/utilisateur`

**Fonctionnalités:**
- ✅ Liste des comptes utilisateurs
- ✅ Filtres par nom, prénom, profil
- ✅ Création de nouveaux comptes
- ✅ Modification (y compris mot de passe)
- ✅ Attribution de profils/rôles multiples
- ✅ Vue des patients suivis

**Champs disponibles:**
- ID Utilisateur (unique)
- Nom, Prénom
- Email (unique)
- Mot de passe (hashé automatiquement)
- Ville, Code Postal
- Profils/Rôles (multi-sélection)

### 🏷️ Gestion des Profils/Rôles
**URL:** `/admin/profil`

**Fonctionnalités:**
- ✅ Liste des rôles système
- ✅ Création de nouveaux profils
- ✅ Modification des rôles
- ✅ Vue des utilisateurs ayant ce profil

**Exemples de rôles:**
- ROLE_ADMIN
- ROLE_MEDECIN
- ROLE_CHIRURGIEN
- ROLE_INFIRMIER
- ROLE_COORDINATEUR

---

## Fonctionnalités Communes

### 🔍 Recherche et Filtres
Chaque page de liste dispose de :
- Barre de recherche rapide
- Filtres avancés par colonne
- Tri sur toutes les colonnes
- Pagination (20 éléments par page)

### ✏️ Actions sur les Enregistrements

**Actions individuelles:**
- 👁️ Voir (détails complets)
- ✏️ Modifier
- 🗑️ Supprimer (avec confirmation)

**Actions groupées:**
- Sélection multiple
- Suppression en masse

### 📋 Formulaires
- Validation en temps réel
- Messages d'aide contextuels
- Champs obligatoires marqués
- Autocomplete sur les relations

---

## Sécurité et Validations

### 🔒 Contrôles d'Intégrité Actifs

**CHECK Constraints:**
- Groupes sanguins valides uniquement
- Dates cohérentes (naissances passées, greffes ≤ aujourd'hui)
- Valeurs positives (poids, durées, débits)
- Côtés anatomiques (Gauche/Droit uniquement)

**Triggers:**
1. **Donneur unique:** Empêche qu'un donneur soit vivant ET décédé
2. **Greffe non fonctionnelle:** Force la saisie de la date et cause de fin
3. **Protection patient:** Empêche la suppression si greffes associées

**Cascades:**
- Suppression donneur → suppression données spécifiques (vivant/décédé)
- Suppression utilisateur → suppression spécialisations (médical/autre)
- Modification ID → propagation automatique

---

## Navigation

### Menu Principal (gauche)
- 🏠 Tableau de bord
- **Gestion des Patients**
  - Patients
  - Greffes
- **Gestion des Donneurs**
  - Donneurs
- **Administration**
  - Utilisateurs
  - Profils/Rôles
- ⬅️ Retour au site
- 🚪 Déconnexion

### Accès Rapide (tableau de bord)
Cartes cliquables pour accès direct à chaque section

---

## Tips et Astuces

### 💡 Bonnes Pratiques

1. **Patients:**
   - Toujours vérifier le N° dossier avant création
   - Assigner un référent médical dès la création

2. **Donneurs:**
   - Bien choisir le type (vivant/décédé) dès le départ
   - Vérifier le groupe sanguin pour compatibilité

3. **Greffes:**
   - Utiliser l'autocomplete pour sélectionner patient/donneur
   - Remplir le compte rendu opératoire complet
   - Si greffon échoue, bien documenter date et cause

4. **Utilisateurs:**
   - Attribuer les bons profils selon les responsabilités
   - Mettre à jour régulièrement les informations

### ⚠️ Points d'Attention

- **Suppression:** Certaines suppressions sont bloquées si dépendances
- **Unicité:** Un donneur ne peut avoir qu'une seule greffe
- **Dates:** Toutes validées automatiquement
- **Triggers:** Messages d'erreur explicites si règle violée

---

## Support Technique

En cas de problème:
1. Vérifier les messages d'erreur affichés
2. Consulter les validations du formulaire
3. Vérifier les contraintes de la base de données

**Contraintes principales:**
- Patient avec greffes → ne peut pas être supprimé
- Donneur avec greffe → ne peut pas être supprimé
- Un donneur = soit vivant, soit décédé (jamais les deux)
- Greffe non fonctionnelle → date fin + cause obligatoires

---

## Prochaines Améliorations Possibles

### 📈 Extensions Non Implémentées (en attente validation)

1. **Tables de référence supplémentaires:**
   - Voie d'abord chirurgicale
   - Cause de décès
   - Statuts virologiques
   - Incompatibilités HLA
   - Risques immunologiques
   - Protocoles immunosuppresseurs

2. **Fonctionnalités avancées:**
   - Export PDF/Excel
   - Statistiques et graphiques
   - Historique des modifications (audit)
   - Recherche plein texte avancée
   - Tableaux de bord personnalisés

3. **Gestion des spécialités:**
   - Donneurs vivants (IMC, créatinine, etc.)
   - Donneurs décédés (critères étendus, etc.)
   - Personnel opératoire
   - Sérologies et HLA détaillés

---

**Version:** 1.0  
**Date:** 1er décembre 2025  
**Base de données:** PostgreSQL avec contraintes complètes
