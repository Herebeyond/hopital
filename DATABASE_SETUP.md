# Installation et Configuration de la Base de Données

## Étapes d'installation

### 1. Créer la base de données

```powershell
php bin/console doctrine:database:create
```

### 2. Créer les tables (migrations)

```powershell
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

### 3. Initialiser les données de test

```powershell
php bin/console app:init-database
```

## Comptes de test créés

### Compte Médecin
- **Email**: medecin@hopital.fr
- **Mot de passe**: password123
- **Rôle**: ROLE_MEDECIN

### Compte Administrateur
- **Email**: admin@hopital.fr
- **Mot de passe**: admin123
- **Rôles**: ROLE_ADMIN, ROLE_MEDECIN

## Structure de la base de données

Les principales entités créées :
- **Utilisateur** : Gère les comptes (avec authentification par email)
- **Profil** : Rôles des utilisateurs (Médecin, Admin, Chirurgien)
- **Patient** : Patients suivis
- **Donneur** : Donneurs d'organes
- **Greffe** : Opérations de greffe (lien Patient-Donneur)

## Accès à l'application

1. Démarrez le serveur Docker si ce n'est pas fait :
```powershell
docker compose up -d
```

2. Accédez à l'application : http://localhost

3. Vous serez redirigé vers la page de connexion

4. Utilisez un des comptes de test ci-dessus

## Commandes utiles

### Créer un nouvel utilisateur manuellement
```powershell
php bin/console doctrine:query:sql "INSERT INTO utilisateur (id, nom, prenom, email, password, ville_res, cp) VALUES ('USER002', 'Nom', 'Prenom', 'email@test.fr', '\$2y\$13\$hashpassword', 'Paris', '75001')"
```

### Réinitialiser la base de données
```powershell
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console app:init-database
```

### Voir toutes les routes
```powershell
php bin/console debug:router
```
