<?php

namespace App\Controller\Admin;

use DateTimeZone;
use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use phpDocumentor\Reflection\Types\Boolean;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }
    

    public function configureFields(string $pageName): iterable
    {
        $currentEntity = $this->getContext()->getEntity()->getInstance();
        if(!$currentEntity) {
            $data = '';
        }else{
            $data = $currentEntity->getDescription();
        }

        return [
            BooleanField::new('isOnline', 'En ligne:'),
            TextField::new('name', 'Nom:'),
            SlugField::new('slug', 'Slug:')->setTargetFieldName('name')->hideOnIndex(),
            // CodeEditorField::new('description', 'Description:'),
            TextEditorField::new('description', 'Description:')->setFormTypeOptions([
                'block_name' => 'custom_description',
                'data' => $data
            ])->setHtmlAttribute('id', 'Article_description')
            ->setColumns(9)->onlyOnForms(),
            // TextareaField::new('description', 'Description:')->setFormType(CKEditorType::class),
            AssociationField::new('category', 'Visible dans la catégorie:'),
            DateTimeField::new('updatedAt', 'Date de mise à jour:')->setDisabled(true),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['id' => 'DESC'])
            ->setPageTitle('index', 'Liste des articles')
            ->setPageTitle('new', 'Nouvel article')
            ->setPageTitle('edit', 'Gestion d\'un article')
            ->setFormThemes(['admin/easyadmin/_tinymce.html.twig','@EasyAdmin/crud/form_theme.html.twig'])
            ->showEntityActionsInlined();
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if($entityInstance instanceof Article) {

            //get current date
            $now = new \DateTimeImmutable('now', new DateTimeZone('Europe/Paris'));

            $entityInstance->setUpdatedAt($now);
            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->persistEntity($entityManager, $entityInstance);
    }

    public function configureAssets(Assets $assets): Assets
    {
        return $assets
            ->addJsFile('https://cdn.tiny.cloud/1/'.$_ENV['TINYMCE_ACCESS_KEY'].'/tinymce/7/tinymce.min.js');
    }
}
