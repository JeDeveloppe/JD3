<?php

namespace App\Controller\Admin;

use DateTimeZone;
use DateTimeImmutable;
use App\Entity\LegalInformation;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class LegalInformationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return LegalInformation::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            
            FormField::addTab('Entreprise'),
            TextField::new('companyName', 'Nom de l\'entreprise:'),
            TextField::new('publicationManagerFirstName', 'Nom du responsable de la publication:')->setColumns(6)->hideOnIndex(),
            TextField::new('publicationManagerLastName', 'Prénom du responsable de la publication:')->setColumns(6)->hideOnIndex(),
            TextField::new('streetCompany', 'Adresse de l\'entreprise:')->setColumns(4),
            IntegerField::new('postalCodeCompany', 'Code postal de l\'entreprise:')->setColumns(3),
            TextField::new('cityCompany', 'Ville de l\'entreprise:')->setColumns(4),
            TextField::new('siretCompany', 'Siret de l\'entreprise:')->setColumns(4)->hideOnIndex(),
            TextField::new('emailCompany', 'Email de l\'entreprise:')->setColumns(4)->hideOnIndex(),
            TextField::new('phoneCompany', 'Téléphone de l\'entreprise:')->setColumns(4)->hideOnIndex(),
            TextField::new('fullUrlCompany', 'Site internet de l\'entreprise:')->setColumns(6)->hideOnIndex(),

            FormField::addTab('Webmaster'),
            TextField::new('webmasterCompanyName', 'Nom de l\'entreprise:')->setColumns(6)->hideOnIndex(),
            TextField::new('webmasterLastName', 'Nom du webmaster:')->setColumns(4)->hideOnIndex(),
            TextField::new('webmasterFirstName', 'Prénom du webmaster:')->setColumns(4)->hideOnIndex(),

            FormField::addTab('Webdesign'),
            TextField::new('webdesignerName', 'Nom complet du webdesigner:')->setColumns(6)->hideOnIndex(),

            FormField::addTab('Hébergeur'),
            TextField::new('hostName', 'Nom de l\'hébergeur:')->hideOnIndex(),
            TextField::new('hostStreet', 'Adresse de l\'hébergeur:')->setColumns(4)->hideOnIndex(),
            IntegerField::new('hostPostalCode', 'Code postal de l\'hébergeur:')->setColumns(3)->hideOnIndex(),
            TextField::new('hostCity', 'Ville de l\'hébergeur:')->setColumns(4)->hideOnIndex(),
            TextField::new('hostPhone', 'Téléphone de l\'hébergeur:')->setColumns(4)->hideOnIndex(),

            FormField::addTab('Mise à jour'),
            DateTimeField::new('updatedAt', 'Dernière mise à jour:')->setDisabled(true)->setColumns(4),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des infos légales')
            ->setPageTitle('new', 'Nouvelles infos légales')
            ->setPageTitle('edit', 'Édition infos légales')
        ;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if($entityInstance instanceof LegalInformation) {

            $now = new DateTimeImmutable('now', new DateTimeZone('Europe/Paris'));
            $entityInstance->setUpdatedAt($now);

            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if($entityInstance instanceof LegalInformation) {

            $now = new DateTimeImmutable('now', new DateTimeZone('Europe/Paris'));
            $entityInstance->setUpdatedAt($now);

            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }
    }

}
