<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Service\TerminalService;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\SlugType;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function __construct(
        private TerminalService $terminalService
    )
    {}
    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom:'),
            SlugField::new('slug', 'Slug:')->setTargetFieldName('name')->hideOnIndex(),
            TextField::new('commandLineInTerminal', 'Commande:'),
            TextField::new('renderIconStringWithoutParentheses', 'Icone:'),
            AssociationField::new('articles', 'Astuces:')
        ];
    }
    
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['id' => 'DESC'])
            ->setPageTitle('index', 'Liste des catégories')
            ->setPageTitle('new', 'Nouvelle catégorie')
            ->setPageTitle('edit', 'Gestion d\'une catégorie')
            ->showEntityActionsInlined();
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if($entityInstance instanceof Category) {

            //get current date
            $now = new \DateTimeImmutable('now', new DateTimeZone('Europe/Paris'));

            $entityInstance->setCreatedAt($now)->setUpdatedAt($now);
            $entityManager->persist($entityInstance);
            $entityManager->flush();

            //execute command
            $command = $entityInstance->getCommandLineInTerminal();
            $this->terminalService->importSymfonyUxIcon($command);

        }
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Category) {

            $this->persistEntity($entityManager, $entityInstance);

        }   
    }
}
