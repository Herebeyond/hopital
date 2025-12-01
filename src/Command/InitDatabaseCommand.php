<?php

namespace App\Command;

use App\Entity\Profil;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:init-database',
    description: 'Initialise la base de données avec des données de test',
)]
class InitDatabaseCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Créer les profils
        $profilMedecin = new Profil();
        $profilMedecin->setId('MEDECIN');
        $profilMedecin->setRole('ROLE_MEDECIN');
        $this->entityManager->persist($profilMedecin);

        $profilAdmin = new Profil();
        $profilAdmin->setId('ADMIN');
        $profilAdmin->setRole('ROLE_ADMIN');
        $this->entityManager->persist($profilAdmin);

        $profilChirurgien = new Profil();
        $profilChirurgien->setId('CHIRURGIEN');
        $profilChirurgien->setRole('ROLE_CHIRURGIEN');
        $this->entityManager->persist($profilChirurgien);

        // Créer un utilisateur de test
        $utilisateur = new Utilisateur();
        $utilisateur->setId('USER001');
        $utilisateur->setNom('Dupont');
        $utilisateur->setPrenom('Jean');
        $utilisateur->setEmail('medecin@hopital.fr');
        $utilisateur->setPassword($this->passwordHasher->hashPassword($utilisateur, 'password123'));
        $utilisateur->setVilleRes('Paris');
        $utilisateur->setCp('75000');
        $utilisateur->addProfil($profilMedecin);
        $this->entityManager->persist($utilisateur);

        // Créer un admin
        $admin = new Utilisateur();
        $admin->setId('ADMIN001');
        $admin->setNom('Martin');
        $admin->setPrenom('Sophie');
        $admin->setEmail('admin@hopital.fr');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $admin->setVilleRes('Paris');
        $admin->setCp('75001');
        $admin->addProfil($profilAdmin);
        $admin->addProfil($profilMedecin);
        $this->entityManager->persist($admin);

        $this->entityManager->flush();

        $io->success('Base de données initialisée avec succès !');
        $io->section('Comptes créés :');
        $io->listing([
            'Médecin - Email: medecin@hopital.fr / Mot de passe: password123',
            'Admin - Email: admin@hopital.fr / Mot de passe: admin123',
        ]);

        return Command::SUCCESS;
    }
}
