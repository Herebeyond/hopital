<?php

namespace App\Controller\Admin;

use App\Entity\Greffe;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;

class GreffeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Greffe::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Greffe')
            ->setEntityLabelInPlural('Greffes')
            ->setDefaultSort(['dateGreffe' => 'DESC'])
            ->setPaginatorPageSize(20);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->hideOnForm();
        
        // Informations principales
        yield AssociationField::new('patient', 'Patient')
            ->setRequired(true)
            ->autocomplete();
        yield AssociationField::new('donneur', 'Donneur')
            ->setRequired(true)
            ->autocomplete();
        yield DateField::new('dateGreffe', 'Date de greffe');
        yield IntegerField::new('rangGreffe', 'Rang de greffe')
            ->setHelp('1 = première greffe, 2 = deuxième, etc.');
        
        yield ChoiceField::new('typeDonneur', 'Type de donneur')
            ->setChoices([
                'Donneur vivant' => 'vivant',
                'Donneur décédé' => 'décédé',
            ]);
        yield TextField::new('typeGreffe', 'Type de greffe');
        
        // État du greffon
        yield BooleanField::new('greffonFonctionnel', 'Greffon fonctionnel');
        yield DateTimeField::new('dateHeureFin', 'Date/heure de fin de fonctionnement')
            ->hideOnIndex();
        yield TextareaField::new('causeFinFonctGreffe', 'Cause de fin de fonctionnement')
            ->hideOnIndex();
        
        // Détails chirurgicaux
        yield DateField::new('dateDeclampage', 'Date de déclampage')
            ->hideOnIndex();
        yield TimeField::new('heureDeclampage', 'Heure de déclampage')
            ->hideOnIndex();
        
        yield ChoiceField::new('cotePrelevementRein', 'Côté prélèvement')
            ->setChoices([
                'Gauche' => 'Gauche',
                'Droit' => 'Droit',
            ])
            ->hideOnIndex();
        
        yield ChoiceField::new('coteTransplantationRein', 'Côté transplantation')
            ->setChoices([
                'Gauche' => 'Gauche',
                'Droit' => 'Droit',
            ])
            ->hideOnIndex();
        
        yield TimeField::new('ischemieTotal', 'Ischémie totale')
            ->setHelp('Durée totale d\'ischémie')
            ->hideOnIndex();
        
        yield IntegerField::new('dureeAnastomoses', 'Durée des anastomoses (min)')
            ->hideOnIndex();
        
        yield BooleanField::new('sondeJj', 'Sonde JJ')
            ->hideOnIndex();
        
        // Protocole et dialyse
        yield BooleanField::new('protocole', 'Protocole de recherche')
            ->hideOnIndex();
        yield TextField::new('commentaireProtocole', 'Commentaire protocole')
            ->hideOnIndex();
        
        yield BooleanField::new('dialyse', 'Patient dialysé')
            ->hideOnIndex();
        yield TextField::new('dateDerniereDialyse', 'Date dernière dialyse')
            ->hideOnIndex();
        
        // Commentaires
        yield TextareaField::new('commentaire', 'Commentaires')
            ->hideOnIndex();
        yield TextareaField::new('compteRenduOperatoire', 'Compte rendu opératoire')
            ->hideOnIndex()
            ->setHelp('Compte rendu détaillé de l\'intervention');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('patient')
            ->add('donneur')
            ->add('dateGreffe')
            ->add('typeDonneur')
            ->add('greffonFonctionnel')
            ->add('rangGreffe');
    }
}
