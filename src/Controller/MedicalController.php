<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Medical Management Controller
 * 
 * This controller manages all medical data for the kidney transplant management system.
 * It provides read-only views for all users, with modification capabilities restricted
 * to users with ROLE_DOCTOR or higher.
 * 
 * Role-Based Access Control:
 * - ROLE_USER: Can view all medical data (patients, donors, transplants, references)
 * - ROLE_NURSE: Same as ROLE_USER (view-only access)
 * - ROLE_DOCTOR: Can view AND modify medical data (add, edit patients/donors/transplants)
 * - ROLE_ADMIN: Full access including deletion and reference data management
 * 
 * The templates use is_granted('ROLE_DOCTOR') to conditionally display:
 * - Add buttons (e.g., "Add Patient", "Add Donor", "Register Transplant")
 * - Edit buttons in list views and detail pages
 * 
 * Delete buttons require ROLE_ADMIN due to data protection regulations.
 * 
 * When implementing create/update/delete methods:
 * - Use #[IsGranted('ROLE_DOCTOR')] for create and update operations
 * - Use #[IsGranted('ROLE_ADMIN')] for delete operations
 */
#[IsGranted('ROLE_USER')]
#[Route('/medical')]
class MedicalController extends AbstractController
{
    public function __construct(
        private Connection $connection
    ) {}

    #[Route('/', name: 'medical_dashboard')]
    public function dashboard(): Response
    {
        // Get statistics directly from database
        $stats = [
            'patients' => $this->connection->fetchOne('SELECT COUNT(*) FROM Patient'),
            'greffes' => $this->connection->fetchOne('SELECT COUNT(*) FROM Greffe'),
            'greffes_actives' => $this->connection->fetchOne('SELECT COUNT(*) FROM Greffe WHERE Greffon_fonctionnel = 1'),
            'donneurs' => $this->connection->fetchOne('SELECT COUNT(*) FROM Donneur'),
            'donneurs_vivants' => $this->connection->fetchOne('SELECT COUNT(*) FROM Donneur_v'),
            'donneurs_decedes' => $this->connection->fetchOne('SELECT COUNT(*) FROM Donneur_d'),
        ];

        // Recent greffes
        $recentGreffes = $this->connection->fetchAllAssociative('
            SELECT 
                g.id_greffon, 
                g.Date_greffe, 
                g.Type_donneur,
                g.Greffon_fonctionnel,
                p.Nom as patient_nom, 
                p.Prenom as patient_prenom,
                p.Ndossier
            FROM Greffe g
            JOIN Patient p ON g.id_patient = p.id_patient
            ORDER BY g.Date_greffe DESC
            LIMIT 10
        ');

        return $this->render('medical/dashboard.html.twig', [
            'stats' => $stats,
            'recent_greffes' => $recentGreffes,
        ]);
    }

    #[Route('/patients', name: 'medical_patients')]
    public function patients(Request $request): Response
    {
        $search = $request->query->get('search', '');
        
        $sql = '
            SELECT 
                p.*,
                u.Nom as medecin_nom,
                u.Prenom as medecin_prenom,
                COUNT(g.id_greffon) as nb_greffes
            FROM Patient p
            LEFT JOIN Utilisateur u ON p.id_utilisateur = u.id_utilisateur
            LEFT JOIN Greffe g ON p.id_patient = g.id_patient
        ';
        
        if ($search) {
            $sql .= ' WHERE p.Nom LIKE :search OR p.Prenom LIKE :search OR p.Ndossier LIKE :search';
        }
        
        $sql .= ' GROUP BY p.id_patient ORDER BY p.Nom, p.Prenom';
        
        if ($search) {
            $patients = $this->connection->fetchAllAssociative($sql, ['search' => "%$search%"]);
        } else {
            $patients = $this->connection->fetchAllAssociative($sql);
        }

        return $this->render('medical/patients.html.twig', [
            'patients' => $patients,
            'search' => $search,
        ]);
    }

    #[Route('/patient/{id}', name: 'medical_patient_detail')]
    public function patientDetail(int $id): Response
    {
        $patient = $this->connection->fetchAssociative('
            SELECT p.*, u.Nom as medecin_nom, u.Prenom as medecin_prenom
            FROM Patient p
            LEFT JOIN Utilisateur u ON p.id_utilisateur = u.id_utilisateur
            WHERE p.id_patient = :id
        ', ['id' => $id]);

        if (!$patient) {
            throw $this->createNotFoundException('Patient non trouvé');
        }

        // Get all transplants for this patient
        $greffes = $this->connection->fetchAllAssociative('
            SELECT 
                g.*,
                d.N_Cristal,
                d.G_sanguin as donneur_groupe,
                ri.Libelle_immunologique,
                ci.Libelle_immunosupresseur
            FROM Greffe g
            LEFT JOIN Donneur d ON g.id_donneur = d.id_donneur
            LEFT JOIN Risque_immunologique ri ON g.id_immunologique = ri.id_immunologique
            LEFT JOIN Conditionnement_immunosupresseur ci ON g.id_immunosupresseur = ci.id_immunosupresseur
            WHERE g.id_patient = :id
            ORDER BY g.Date_greffe DESC
        ', ['id' => $id]);

        return $this->render('medical/patient_detail.html.twig', [
            'patient' => $patient,
            'greffes' => $greffes,
        ]);
    }

    #[Route('/donneurs', name: 'medical_donneurs')]
    public function donneurs(Request $request): Response
    {
        $type = $request->query->get('type', 'all'); // all, vivant, decede
        $search = $request->query->get('search', '');
        
        $sql = 'SELECT d.*, ';
        
        if ($type === 'vivant') {
            $sql .= 'dv.Nom, dv.Prenom, dv.IMC, "vivant" as type_donneur, lp.Lib_Lien
                     FROM Donneur d
                     JOIN Donneur_v dv ON d.id_donneur = dv.id_donneur
                     LEFT JOIN Lien_parente lp ON dv.id_lien = lp.id_lien
                     WHERE 1=1';
        } elseif ($type === 'decede') {
            $sql .= 'dd.Ville_origine, "décédé" as type_donneur, cd.Lib_deces
                     FROM Donneur d
                     JOIN Donneur_d dd ON d.id_donneur = dd.id_donneur
                     LEFT JOIN Cause_deces cd ON dd.id_deces = cd.id_deces
                     WHERE 1=1';
        } else {
            $sql = '
                SELECT d.*, 
                    dv.Nom, dv.Prenom,
                    CASE 
                        WHEN dv.id_donneur_v IS NOT NULL THEN "vivant"
                        WHEN dd.id_donneur_d IS NOT NULL THEN "décédé"
                        ELSE "inconnu"
                    END as type_donneur
                FROM Donneur d
                LEFT JOIN Donneur_v dv ON d.id_donneur = dv.id_donneur
                LEFT JOIN Donneur_d dd ON d.id_donneur = dd.id_donneur
                WHERE 1=1
            ';
        }
        
        if ($search) {
            $sql .= ' AND (d.N_Cristal LIKE :search OR d.id_donneur LIKE :search';
            if ($type === 'vivant' || $type === 'all') {
                $sql .= ' OR dv.Nom LIKE :search OR dv.Prenom LIKE :search';
            }
            $sql .= ')';
        }
        
        $sql .= ' ORDER BY d.id_donneur DESC';
        
        if ($search) {
            $donneurs = $this->connection->fetchAllAssociative($sql, ['search' => "%$search%"]);
        } else {
            $donneurs = $this->connection->fetchAllAssociative($sql);
        }

        return $this->render('medical/donneurs.html.twig', [
            'donneurs' => $donneurs,
            'type' => $type,
            'search' => $search,
        ]);
    }

    #[Route('/donneur/{id}', name: 'medical_donneur_detail')]
    public function donneurDetail(string $id): Response
    {
        $donneur = $this->connection->fetchAssociative('
            SELECT d.* FROM Donneur d WHERE d.id_donneur = :id
        ', ['id' => $id]);

        if (!$donneur) {
            throw $this->createNotFoundException('Donneur non trouvé');
        }

        // Check if living or deceased
        $donneurVivant = $this->connection->fetchAssociative('
            SELECT dv.*, lp.Lib_Lien, va.Lib_voie
            FROM Donneur_v dv
            LEFT JOIN Lien_parente lp ON dv.id_lien = lp.id_lien
            LEFT JOIN Voie_abord va ON dv.id_voie = va.id_voie
            WHERE dv.id_donneur = :id
        ', ['id' => $id]);

        $donneurDecede = $this->connection->fetchAssociative('
            SELECT dd.*, cd.Lib_deces
            FROM Donneur_d dd
            LEFT JOIN Cause_deces cd ON dd.id_deces = cd.id_deces
            WHERE dd.id_donneur = :id
        ', ['id' => $id]);

        // Get HLA typing
        $hlaTyping = $this->connection->fetchAllAssociative('
            SELECT vgh.*, gh.Libelle_groupe_HLA
            FROM Valeur_groupe_HLA vgh
            LEFT JOIN Groupe_HLA gh ON vgh.id_groupe_HLA = gh.id_groupe_HLA
            WHERE vgh.id_donneur = :id
        ', ['id' => $id]);

        // Get serology results
        $serologies = $this->connection->fetchAllAssociative('
            SELECT gs.Libelle_serologie, vs.Libelle_valeur_serologie
            FROM Serologie_Donneur sd
            LEFT JOIN Groupe_serologie gs ON sd.id_serologie = gs.id_serologie
            LEFT JOIN Valeur_serologie vs ON sd.id_valeur_serologie = vs.id_valeur_serologie
            WHERE sd.id_donneur = :id
        ', ['id' => $id]);

        // Get associated transplants
        $greffes = $this->connection->fetchAllAssociative('
            SELECT g.*, p.Nom as patient_nom, p.Prenom as patient_prenom, p.Ndossier
            FROM Greffe g
            LEFT JOIN Patient p ON g.id_patient = p.id_patient
            WHERE g.id_donneur = :id
        ', ['id' => $id]);

        return $this->render('medical/donneur_detail.html.twig', [
            'donneur' => $donneur,
            'donneur_vivant' => $donneurVivant,
            'donneur_decede' => $donneurDecede,
            'hla_typing' => $hlaTyping,
            'serologies' => $serologies,
            'greffes' => $greffes,
        ]);
    }

    #[Route('/greffes', name: 'medical_greffes')]
    public function greffes(Request $request): Response
    {
        $status = $request->query->get('status', 'all'); // all, actif, inactif
        $search = $request->query->get('search', '');
        
        $sql = '
            SELECT 
                g.*,
                p.Nom as patient_nom,
                p.Prenom as patient_prenom,
                p.Ndossier,
                d.N_Cristal,
                ri.Libelle_immunologique,
                ci.Libelle_immunosupresseur
            FROM Greffe g
            LEFT JOIN Patient p ON g.id_patient = p.id_patient
            LEFT JOIN Donneur d ON g.id_donneur = d.id_donneur
            LEFT JOIN Risque_immunologique ri ON g.id_immunologique = ri.id_immunologique
            LEFT JOIN Conditionnement_immunosupresseur ci ON g.id_immunosupresseur = ci.id_immunosupresseur
            WHERE 1=1
        ';
        
        if ($status === 'actif') {
            $sql .= ' AND g.Greffon_fonctionnel = 1';
        } elseif ($status === 'inactif') {
            $sql .= ' AND g.Greffon_fonctionnel = 0';
        }
        
        if ($search) {
            $sql .= ' AND (p.Nom LIKE :search OR p.Prenom LIKE :search OR p.Ndossier LIKE :search OR d.N_Cristal LIKE :search)';
        }
        
        $sql .= ' ORDER BY g.Date_greffe DESC';
        
        if ($search) {
            $greffes = $this->connection->fetchAllAssociative($sql, ['search' => "%$search%"]);
        } else {
            $greffes = $this->connection->fetchAllAssociative($sql);
        }

        return $this->render('medical/greffes.html.twig', [
            'greffes' => $greffes,
            'status' => $status,
            'search' => $search,
        ]);
    }

    #[Route('/greffe/{id}', name: 'medical_greffe_detail')]
    public function greffeDetail(int $id): Response
    {
        $greffe = $this->connection->fetchAssociative('
            SELECT 
                g.*,
                p.Nom as patient_nom,
                p.Prenom as patient_prenom,
                p.Ndossier,
                p.Date_naissance as patient_naissance,
                d.N_Cristal,
                d.G_sanguin as donneur_groupe,
                d.Sexe as donneur_sexe,
                d.Age as donneur_age,
                ri.Libelle_immunologique,
                ri.Commentaire_risque,
                ci.Libelle_immunosupresseur,
                ci.Commentaire_immunosupresseur
            FROM Greffe g
            LEFT JOIN Patient p ON g.id_patient = p.id_patient
            LEFT JOIN Donneur d ON g.id_donneur = d.id_donneur
            LEFT JOIN Risque_immunologique ri ON g.id_immunologique = ri.id_immunologique
            LEFT JOIN Conditionnement_immunosupresseur ci ON g.id_immunosupresseur = ci.id_immunosupresseur
            WHERE g.id_greffon = :id
        ', ['id' => $id]);

        if (!$greffe) {
            throw $this->createNotFoundException('Greffe non trouvée');
        }

        // Get HLA incompatibilities
        $hlaIncompatibilites = $this->connection->fetchAllAssociative('
            SELECT ih.Libelle_HLA, vih.Libelle_val_HLA
            FROM Incompatibilite_HLA_Greffe ihg
            LEFT JOIN Incompatibilite_HLA ih ON ihg.id_HLA = ih.id_HLA
            LEFT JOIN Valeur_I_HLA vih ON ihg.id_val_HLA = vih.id_val_HLA
            WHERE ihg.id_greffon = :id
        ', ['id' => $id]);

        // Get virological status
        $statutsVirologiques = $this->connection->fetchAllAssociative('
            SELECT sv.Libelle_SV, vv.Libelle_val_statut
            FROM Statut_Virologique_Greffe svg
            LEFT JOIN Statut_virologique sv ON svg.id_SV = sv.id_SV
            LEFT JOIN Valeur_viriologique vv ON svg.id_val_statut = vv.id_val_statut
            WHERE svg.id_greffon = :id
        ', ['id' => $id]);

        return $this->render('medical/greffe_detail.html.twig', [
            'greffe' => $greffe,
            'hla_incompatibilites' => $hlaIncompatibilites,
            'statuts_virologiques' => $statutsVirologiques,
        ]);
    }

    #[Route('/references', name: 'medical_references')]
    public function references(): Response
    {
        $data = [
            'voies_abord' => $this->connection->fetchAllAssociative('SELECT * FROM Voie_abord ORDER BY Lib_voie'),
            'causes_deces' => $this->connection->fetchAllAssociative('SELECT * FROM Cause_deces ORDER BY Lib_deces'),
            'liens_parente' => $this->connection->fetchAllAssociative('SELECT * FROM Lien_parente ORDER BY Lib_Lien'),
            'risques_immunologiques' => $this->connection->fetchAllAssociative('SELECT * FROM Risque_immunologique ORDER BY Libelle_immunologique'),
            'conditionnements' => $this->connection->fetchAllAssociative('SELECT * FROM Conditionnement_immunosupresseur ORDER BY Libelle_immunosupresseur'),
            'groupes_hla' => $this->connection->fetchAllAssociative('SELECT * FROM Groupe_HLA ORDER BY Libelle_groupe_HLA'),
            'incompatibilites_hla' => $this->connection->fetchAllAssociative('SELECT * FROM Incompatibilite_HLA ORDER BY Libelle_HLA'),
            'statuts_virologiques' => $this->connection->fetchAllAssociative('SELECT * FROM Statut_virologique ORDER BY Libelle_SV'),
            'groupes_serologie' => $this->connection->fetchAllAssociative('SELECT * FROM Groupe_serologie ORDER BY Libelle_serologie'),
        ];

        return $this->render('medical/references.html.twig', [
            'data' => $data,
        ]);
    }

    /*
     * ===================================================================
     * EXAMPLE: ROLE-PROTECTED CREATE/UPDATE/DELETE METHODS
     * ===================================================================
     * 
     * When implementing data modification endpoints, use role-based attributes
     * to restrict access appropriately.
     * 
     * Example implementation patterns:
     * 
     * // CREATE (Doctors can create patients/donors/transplants)
     * #[Route('/patient/new', name: 'medical_patient_new')]
     * #[IsGranted('ROLE_DOCTOR')]
     * public function newPatient(Request $request): Response
     * {
     *     // Users with ROLE_DOCTOR or higher can access this
     *     // Handle form submission and create patient
     * }
     * 
     * // UPDATE (Doctors can update medical records)
     * #[Route('/patient/{id}/edit', name: 'medical_patient_edit')]
     * #[IsGranted('ROLE_DOCTOR')]
     * public function editPatient(int $id, Request $request): Response
     * {
     *     // Users with ROLE_DOCTOR or higher can access this
     *     // Handle form submission and update patient
     * }
     * 
     * // DELETE (Only admins can delete due to data protection regulations)
     * #[Route('/patient/{id}/delete', name: 'medical_patient_delete', methods: ['POST'])]
     * #[IsGranted('ROLE_ADMIN')]
     * public function deletePatient(int $id): Response
     * {
     *     // Only users with ROLE_ADMIN can access this
     *     // Handle deletion with proper checks and audit logging
     * }
     * 
     * The buttons for these actions are already in the templates:
     * - Add/Edit buttons: {% if is_granted('ROLE_DOCTOR') %}
     * - Delete buttons: {% if is_granted('ROLE_ADMIN') %}
     */
}
