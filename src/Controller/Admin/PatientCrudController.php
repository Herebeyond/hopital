<?php

namespace App\Controller\Admin;

use App\Entity\Patient;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;

class PatientCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Patient::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Patient')
            ->setEntityLabelInPlural('Patients')
            ->setSearchFields(['ndossier', 'nom', 'prenom'])
            ->setPaginatorPageSize(20);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('ndossier', 'N° Dossier')
            ->setRequired(true)
            ->setHelp('Numéro de dossier hospitalier');
        yield TextField::new('nom', 'Nom');
        yield TextField::new('prenom', 'Prénom');
        yield DateField::new('dateNaissance', 'Date de naissance');
        yield TextField::new('cp', 'Code Postal');
        yield TextField::new('villeRes', 'Ville');
        yield AssociationField::new('utilisateur', 'Référent médical')
            ->setRequired(true);
        yield AssociationField::new('greffes', 'Greffes')
            ->onlyOnDetail()
            ->setTemplatePath('admin/field/greffes_list.html.twig');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('nom')
            ->add('prenom')
            ->add('villeRes')
            ->add('utilisateur');
    }
}
