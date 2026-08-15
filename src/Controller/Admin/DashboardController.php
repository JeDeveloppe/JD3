<?php

namespace App\Controller\Admin;

use App\Controller\Admin\CategoryCrudController;
use App\Controller\Admin\LegalInformationCrudController;
use App\Controller\Admin\ProjectCrudController;
use App\Controller\Admin\TechnologyCrudController;
use App\Controller\Admin\TechnologyFamilyCrudController;
use App\Controller\Admin\TrainingCrudController;
use App\Controller\Admin\ArticleCrudController;
use App\Controller\Admin\CvCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {

        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('JD3');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToRoute('Site', 'fa-solid fa-earth-europe', 'site_home');

        yield MenuItem::section('Blog');
        yield MenuItem::linkTo(CategoryCrudController::class, 'Les catégories', 'fas fa-list');
        yield MenuItem::linkTo(ArticleCrudController::class, 'Les articles', 'fas fa-list');

        yield MenuItem::section('Projets');
        yield MenuItem::linkTo(ProjectCrudController::class, 'Les projets', 'fas fa-list');
        yield MenuItem::linkTo(TechnologyCrudController::class, 'Les technologies', 'fas fa-list');

        yield MenuItem::section('Formations');
        yield MenuItem::linkTo(TrainingCrudController::class, 'Les formations', 'fas fa-list');

        yield MenuItem::section('Paramètres du site');
        yield MenuItem::linkTo(TechnologyFamilyCrudController::class, 'Les familles technologiques', 'fas fa-list');
        yield MenuItem::linkTo(LegalInformationCrudController::class, 'Les infos légales', 'fas fa-list');

        yield MenuItem::section('CV');
        yield MenuItem::linkTo(CvCrudController::class, 'Les vues du CV', 'fas fa-list');
    }
}
