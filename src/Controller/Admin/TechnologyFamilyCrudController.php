<?php

namespace App\Controller\Admin;

use App\Entity\TechnologyFamily;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class TechnologyFamilyCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TechnologyFamily::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom:'),
            TextField::new('description', 'Description:'),
            IntegerField::new('orderOfAppearance', 'Ordre d\'affichage:')->setFormTypeOptions(['attr' => ['min' => 0, 'max' => 100]]),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['name' => 'ASC'])
            ->setPageTitle('index', 'Liste des familles de technologies')
            ->setPageTitle('new', 'Nouvelle famille')
            ->setPageTitle('edit', 'Gestion d\'une famille')
            ->showEntityActionsInlined();
    }

}
