<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        // Rediriger vers login si non connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('home/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/patients', name: 'app_patients')]
    public function patients(): Response
    {
        return $this->render('home/page/patients.html.twig');
    }

    #[Route('/liste_attente', name: 'app_liste_att')]
    public function liste_att(): Response
    {
        return $this->render('home/page/liste_attente.html.twig');
    }
    
    #[Route('/greffons', name: 'app_greffons')]
    public function greffons(): Response
    {
        return $this->render('home/page/greffons.html.twig');
    }

    #[Route('/analyse', name: 'app_analyse')]
    public function analyse(): Response
    {
        return $this->render('home/page/analyse.html.twig');
    }

}
