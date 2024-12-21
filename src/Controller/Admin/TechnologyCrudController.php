<?php

namespace App\Controller\Admin;

use App\Entity\Technology;
use App\Service\TerminalService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class TechnologyCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Technology::class;
    }

    public function __construct(
        private TerminalService $terminalService
    )
    {}


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom:'),
            AssociationField::new('family', 'Famille:')->setRequired(true),
            TextField::new('description', 'Description:'),
            TextField::new('commandLineInTerminal', 'Commande:'),
            TextField::new('renderIconStringWithoutParentheses', 'Icone:'),
            IntegerField::new('knowledgeRate', 'Niveau de maîtrise:')->setFormTypeOptions(['attr' => ['min' => 0, 'max' => 100]]),
            IntegerField::new('orderOfAppearance', 'Ordre d\'affichage:')->setFormTypeOptions(['attr' => ['min' => 0, 'max' => 100]]),
        ];
    }
    
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['orderOfAppearance' => 'ASC'])
            ->setPageTitle('index', 'Liste des technologies')
            ->setPageTitle('new', 'Nouvelle technologie')
            ->setPageTitle('edit', 'Gestion d\'une technologie')
            ->showEntityActionsInlined();
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Technology) {

            //execute command
            $this->terminalService->importSymfonyUxIcon($entityInstance->getCommandLineInTerminal());

            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }

    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Technology) {

            //execute command
            $this->terminalService->importSymfonyUxIcon($entityInstance->getCommandLineInTerminal());

            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }
    }
}
