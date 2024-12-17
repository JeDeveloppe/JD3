<?php

namespace App\Controller\Site;

use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SiteController extends AbstractController
{
    public function __construct(
        private ProjectRepository $projectRepository
    )
    {}

    #[Route('/', name: 'site_home')]
    public function index(): Response
    {
        return $this->render('site/index.html.twig', [
            'controller_name' => 'SiteController',
        ]);
    }

    #[Route('/mes-realisations', name: 'site_my_works')]
    public function myWorks(): Response
    {
        $projects = $this->projectRepository->findAll();

        return $this->render('site/works/myworks.html.twig', [
            'projects' => $projects,
            'h1' => 'Mes Realisations'
        ]);
    }
}
