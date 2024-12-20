<?php

namespace App\Controller\Admin;

use App\Entity\Trick;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class TrickCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Trick::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom:'),
            SlugField::new('slug', 'Slug:')->setTargetFieldName('name')->hideOnIndex(),
            TextEditorField::new('description', 'Description:'),
            AssociationField::new('category', 'Visible dans la catégorie:'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['id' => 'DESC'])
            ->setPageTitle('index', 'Liste des astuces')
            ->setPageTitle('new', 'Nouvelle astuce')
            ->setPageTitle('edit', 'Gestion d\'une astuce')
            ->showEntityActionsInlined();
    }

}
