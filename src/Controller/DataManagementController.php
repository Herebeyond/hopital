<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class DataManagementController extends AbstractController
{
    #[Route('/admin/data-management', name: 'admin_data_management')]
    public function index(EntityManagerInterface $em): Response
    {
        // Get statistics
        $stats = [
            'patients' => $em->getRepository('App\Entity\Patient')->count([]),
            'donneurs' => $em->getRepository('App\Entity\Donneur')->count([]),
            'greffes' => $em->getRepository('App\Entity\Greffe')->count([]),
            'utilisateurs' => $em->getRepository('App\Entity\Utilisateur')->count([]),
            'profils' => $em->getRepository('App\Entity\Profil')->count([]),
        ];

        return $this->render('admin/data_management.html.twig', [
            'stats' => $stats,
        ]);
    }

    #[Route('/admin/init-reference-data', name: 'admin_init_reference_data')]
    public function initReferenceData(EntityManagerInterface $em): Response
    {
        try {
            // This would initialize reference tables like Voie_abord, Cause_deces, etc.
            // Currently the database has these tables but no Doctrine entities
            
            $this->addFlash('success', 'Les données de référence seront ajoutées lorsque les entités seront créées.');
            
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'initialisation: ' . $e->getMessage());
        }

        return $this->redirectToRoute('admin_data_management');
    }
}
