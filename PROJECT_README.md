# 🫀 Plateforme Médicale de Greffe - Projet d'Exercice

Système de gestion des transplantations d'organes destiné aux professionnels de santé.

## 🚀 Démarrage Rapide

### 1. Lancer Docker
```powershell
docker compose up -d
```

### 2. Installer les dépendances
```powershell
docker compose exec php composer install
```

### 3. Créer la base de données
```powershell
docker compose exec php php bin/console doctrine:database:create
docker compose exec php php bin/console doctrine:migrations:diff
docker compose exec php php bin/console doctrine:migrations:migrate
docker compose exec php php bin/console app:init-database
```

### 4. Accéder à l'application
Ouvrez votre navigateur : **http://localhost**

## 🔑 Comptes de Test

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Médecin | medecin@hopital.fr | password123 |
| Administrateur | admin@hopital.fr | admin123 |

## 📊 Base de Données

### Schéma Principal

**Entités créées :**
- `Utilisateur` - Comptes des professionnels (authentification par email)
- `Profil` - Rôles (ROLE_MEDECIN, ROLE_ADMIN, ROLE_CHIRURGIEN)
- `Patient` - Dossiers patients
- `Donneur` - Informations donneurs
- `Greffe` - Opérations de transplantation
- `Donneur_v` - Donneurs vivants
- `Donneur_d` - Donneurs décédés

**Relations :**
- Un utilisateur peut avoir plusieurs profils (table `etre`)
- Un patient est suivi par un utilisateur (médecin référent)
- Une greffe lie un patient à un donneur
- Traçabilité complète des interventions

### Tables de Référence
- `Lien_parente` - Liens de parenté (donneurs vivants)
- `Voie_abord` - Voies d'abord chirurgicales
- `Cause_deces` - Causes de décès (donneurs décédés)
- `Statut_virologique` - Statuts virologiques
- `Incompatibilite_HLA` - Typage HLA
- `Groupe_HLA` - Groupage HLA
- `Conditionnement_immunosupresseur` - Protocoles immunosuppresseurs
- `Risque_immunologique` - Risques immunologiques

## 🏗️ Architecture

### Stack Technique
- **Backend** : Symfony 7.x + PHP 8.2
- **Base de données** : PostgreSQL 16
- **Serveur** : FrankenPHP (Docker)
- **ORM** : Doctrine
- **Sécurité** : Symfony Security Component

### Structure du Projet
```
src/
├── Controller/
│   ├── HomeController.php       # Page d'accueil
│   └── SecurityController.php   # Authentification
├── Entity/
│   ├── Utilisateur.php         # Entité User
│   ├── Patient.php
│   ├── Donneur.php
│   ├── Greffe.php
│   └── Profil.php
├── Repository/
│   └── ...Repository.php       # Repositories Doctrine
└── Command/
    └── InitDatabaseCommand.php # Commande d'initialisation

templates/
├── home/
│   └── index.html.twig         # Page d'accueil
├── security/
│   └── login.html.twig         # Page de connexion
└── base.html.twig              # Template de base

assets/
└── styles/
    └── app.css                 # Styles globaux (inclut login & home)
```

## 🎨 Pages Disponibles

### Page d'Accueil (/)
- Accessible uniquement aux utilisateurs connectés
- Présentation des modules de gestion
- Workflow médical
- Navigation vers les différentes sections

### Page de Connexion (/login)
- Authentification par email + mot de passe
- Protection CSRF
- Option "Rester connecté"
- Comptes de test affichés

## 🔒 Sécurité

- **Authentification** : Form Login avec email
- **Hashage** : Bcrypt automatique
- **CSRF** : Protection activée
- **Remember Me** : Session de 7 jours
- **Access Control** : Toutes les pages nécessitent ROLE_USER sauf /login

## 🛠️ Commandes Utiles

### Base de données
```powershell
# Créer une migration
docker compose exec php php bin/console doctrine:migrations:diff

# Exécuter les migrations
docker compose exec php php bin/console doctrine:migrations:migrate

# Réinitialiser la BDD
docker compose exec php php bin/console doctrine:database:drop --force
docker compose exec php php bin/console doctrine:database:create
docker compose exec php php bin/console doctrine:migrations:migrate
docker compose exec php php bin/console app:init-database
```

### Debug
```powershell
# Voir les routes
docker compose exec php php bin/console debug:router

# Voir la configuration de sécurité
docker compose exec php php bin/console debug:security

# Vider le cache
docker compose exec php php bin/console cache:clear
```

## 📝 Notes Importantes

### Exercice vs Production
Ce projet est un **exercice pédagogique**. En production réelle :
- Utiliser le RPPS (numéro professionnel) au lieu de l'email
- Implémenter l'authentification 2FA
- Ajouter logs d'audit détaillés
- Conformité RGPD/HDS stricte
- Sauvegarde automatique
- Chiffrement des données sensibles

### Extensions Possibles
- [ ] Tableau de bord avec statistiques
- [ ] Gestion complète des patients
- [ ] Interface de saisie des greffes
- [ ] Système d'alerte (greffons disponibles)
- [ ] Intégration système CRISTAL
- [ ] Exports PDF (comptes-rendus)
- [ ] API REST pour applications mobiles
- [ ] Module de recherche avancée

## 📚 Documentation

- [Configuration de la base de données](DATABASE_SETUP.md)
- [Documentation Symfony](https://symfony.com/doc/current/index.html)
- [Doctrine ORM](https://www.doctrine-project.org/projects/orm.html)

## 👨‍💻 Développement

### Logs
```powershell
# Logs Docker
docker compose logs -f

# Logs Symfony
tail -f var/log/dev.log
```

### Tests
```powershell
docker compose exec php php bin/phpunit
```

---

**Note** : Site d'exercice - Données de santé simulées - Conformité HDS non requise pour ce projet académique.
