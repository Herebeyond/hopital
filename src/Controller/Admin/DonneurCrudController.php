<?php

namespace App\Controller\Admin;

use App\Entity\Donneur;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;

class DonneurCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Donneur::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Donneur')
            ->setEntityLabelInPlural('Donneurs')
            ->setSearchFields(['nCristal', 'commentairePatient'])
            ->setPaginatorPageSize(20);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('nCristal', 'N° Cristal')
            ->setHelp('Numéro d\'identification Cristal');
        
        yield ChoiceField::new('gSanguin', 'Groupe sanguin')
            ->setChoices([
                'A+' => 'A+',
                'A-' => 'A-',
                'B+' => 'B+',
                'B-' => 'B-',
                'AB+' => 'AB+',
                'AB-' => 'AB-',
                'O+' => 'O+',
                'O-' => 'O-',
            ]);
        
        yield BooleanField::new('sexe', 'Sexe')
            ->renderAsSwitch(false)
            ->setHelp('Homme = Oui, Femme = Non');
        
        yield DateField::new('age', 'Date de naissance')
            ->setHelp('Champ "Age" dans la base de données');
        
        yield TextField::new('poids', 'Poids (kg)');
        
        yield TextField::new('commentairePatient', 'Commentaire')
            ->hideOnIndex();
        
        yield TextField::new('greffe', 'Greffe associée')
            ->onlyOnDetail()
            ->setTemplatePath('admin/field/greffe_detail.html.twig');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('gSanguin')
            ->add('sexe')
            ->add('nCristal');
    }
}
