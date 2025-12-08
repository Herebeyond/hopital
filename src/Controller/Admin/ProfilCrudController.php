<?php

namespace App\Controller\Admin;

use App\Entity\Profil;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class ProfilCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Profil::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Profil')
            ->setEntityLabelInPlural('Profils')
            ->setSearchFields(['role'])
            ->setDefaultSort(['role' => 'ASC'])
            ->setPaginatorPageSize(20);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID Profil')
            ->setHelp('Identifiant unique du profil');
        
        yield TextField::new('role', 'Rôle')
            ->setHelp('Ex: ROLE_ADMIN, ROLE_MEDECIN, ROLE_CHIRURGIEN');
        
        yield AssociationField::new('utilisateurs', 'Utilisateurs')
            ->onlyOnDetail()
            ->setHelp('Utilisateurs ayant ce profil');
    }
}
