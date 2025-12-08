<?php

namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UtilisateurCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Utilisateur::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')
            ->setSearchFields(['nom', 'prenom', 'email'])
            ->setDefaultSort(['nom' => 'ASC'])
            ->setPaginatorPageSize(20);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID Utilisateur');
        yield TextField::new('nom', 'Nom');
        yield TextField::new('prenom', 'Prénom');
        yield EmailField::new('email', 'Email');
        
        yield TextField::new('password', 'Mot de passe')
            ->setFormType(PasswordType::class)
            ->onlyOnForms()
            ->setHelp('Laissez vide pour conserver le mot de passe actuel');
        
        yield TextField::new('villeRes', 'Ville');
        yield TextField::new('cp', 'Code Postal');
        
        yield AssociationField::new('profils', 'Profils/Rôles')
            ->setHelp('Rôles attribués à cet utilisateur');
        
        yield AssociationField::new('patients', 'Patients suivis')
            ->onlyOnDetail()
            ->setTemplatePath('admin/field/patients_list.html.twig');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('nom')
            ->add('prenom')
            ->add('email')
            ->add('profils')
            ->add('villeRes');
    }
}
