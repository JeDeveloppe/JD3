<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\LegalInformation;
use App\Entity\Project;
use App\Entity\Technology;
use App\Entity\TechnologyFamily;
use App\Entity\Training;
use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
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
        yield MenuItem::linkToCrud('Les catégories', 'fas fa-list', Category::class);
        yield MenuItem::linkToCrud('Les articles', 'fas fa-list', Article::class);

        yield MenuItem::section('Projets');
        yield MenuItem::linkToCrud('Les projets', 'fas fa-list', Project::class);
        yield MenuItem::linkToCrud('Les technologies', 'fas fa-list', Technology::class);

        yield MenuItem::section('Formations');
        yield MenuItem::linkToCrud('Les formations', 'fas fa-list', Training::class);

        yield MenuItem::section('Paramètres du site');
        yield MenuItem::linkToCrud('Les familles technologiques', 'fas fa-list', TechnologyFamily::class);
        yield MenuItem::linkToCrud('Les infos légales', 'fas fa-list', LegalInformation::class);
    }
}
