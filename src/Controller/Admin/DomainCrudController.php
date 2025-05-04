<?php

namespace App\Controller\Admin;

use App\Entity\Domain;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class DomainCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Domain::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom:'),
            SlugField::new('slug', 'Slug:')->setTargetFieldName('name')->hideOnIndex(),
            TextField::new('commandLineInTerminal', 'Commande:')->setRequired(true),
            TextField::new('renderIconStringWithoutParentheses', 'Icone:')->setRequired(true),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['id' => 'DESC'])
            ->setPageTitle('index', 'Liste des domaines')
            ->setPageTitle('new', 'Nouveau domaine')
            ->setPageTitle('edit', 'Gestion d\'un domaine')
            ->setPageTitle('detail', 'Domaine')
            ->showEntityActionsInlined();
    }
}
