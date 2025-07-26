<?php

namespace App\Controller\Admin;

use DateTime;
use App\Entity\Project;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use phpDocumentor\Reflection\Types\Boolean;

class ProjectCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Project::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            BooleanField::new('isOnline', 'En ligne:'),
            TextField::new('name'),
            TextEditorField::new('description'),
            ImageField::new('imageName', 'Image:')
                ->setBasePath($this->getParameter('app.path.projects_images'))
                ->onlyOnIndex(),
            TextField::new('imageFile')
                ->setFormType(VichImageType::class)
                ->setFormTypeOptions([
                    'required' => false,
                    'allow_delete' => false,
                    'delete_label' => 'Supprimer du serveur ?',
                    'download_label' => '...',
                    'download_uri' => true,
                    'image_uri' => true,
                    // 'imagine_pattern' => '...',
                    'asset_helper' => true,
                ])
                ->setLabel('Image')
                ->onlyOnForms()
                ->setColumns(12),
            DateTimeField::new('startedAt','Début du projet:')
                ->setColumns(3),
            DateTimeField::new('endAt', 'Fin du projet:')
                ->setColumns(3),
            TextField::new('url','Lien vers le projet:')
                ->setColumns(6),
            AssociationField::new('technologies', 'Technologies:'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Liste des projets')
            ->setPageTitle('new', 'Nouveau projet')
            ->setPageTitle('edit', 'Gestion d\'un projet')
            ->setDefaultSort(['startedAt' => 'DESC']);
    }

}
