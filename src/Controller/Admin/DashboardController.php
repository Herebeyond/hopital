<?php

namespace App\Controller\Admin;

use App\Entity\Donneur;
use App\Entity\Greffe;
use App\Entity\Patient;
use App\Entity\Profil;
use App\Entity\Utilisateur;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Gestion Hospitalière - Greffes Rénales')
            ->setFaviconPath('favicon.ico');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');
        
        yield MenuItem::section('Gestion des Patients');
        yield MenuItem::linkToCrud('Patients', 'fa fa-user-injured', Patient::class);
        yield MenuItem::linkToCrud('Greffes', 'fa fa-heart', Greffe::class);
        
        yield MenuItem::section('Gestion des Donneurs');
        yield MenuItem::linkToCrud('Donneurs', 'fa fa-hand-holding-heart', Donneur::class);
        
        yield MenuItem::section('Administration');
        yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-users', Utilisateur::class);
        yield MenuItem::linkToCrud('Profils/Rôles', 'fa fa-user-tag', Profil::class);
        yield MenuItem::linkToRoute('Gestion des Données', 'fa fa-database', 'admin_data_management');
        
        yield MenuItem::section('');
        yield MenuItem::linkToRoute('Retour au site', 'fa fa-arrow-left', 'app_home');
        yield MenuItem::linkToLogout('Déconnexion', 'fa fa-sign-out-alt');
    }
}
