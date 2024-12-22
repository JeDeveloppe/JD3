<?php

namespace App\Controller\Admin;

use App\Entity\Training;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class TrainingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Training::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            BooleanField::new('isOnline', 'En ligne:'),
            TextField::new('name', 'Nom:'),
            TextField::new('diplomeName', 'Diplome:'),
            TextEditorField::new('description', 'Description:')
                ->onlyOnForms()
                ->setColumns(12),
            DateTimeField::new('startedAt','Début de la formation:')
                ->setColumns(3),
            DateTimeField::new('endAt', 'Fin de la formation:')
                ->setColumns(3),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des formations')
            ->setPageTitle('new', 'Nouvelle forrmation')
            ->setPageTitle('edit', 'Gestion d\'une formation')
            ->setDefaultSort(['startedAt' => 'DESC']);
    }
}
