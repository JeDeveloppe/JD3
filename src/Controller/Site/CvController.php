<?php

namespace App\Controller\Site;

use App\Service\CvService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class CvController extends AbstractController
{
    #[Route('/cv-manager', name: 'app_cv')]
    public function index(CvService $cvService): Response
    {
        // 1. Appelle le service pour gérer la vue (incrémente si nouvelle session)
        // et récupère le nombre total de vues depuis la base de données.
        $totalViews = $cvService->returnNumberOfViewsAndAddNewView();
        
        // 2. Transmet le nombre de vues à la vue Twig
        return $this->render('cv/cv-manager.html.twig', [
            'controller_name' => 'CvController',
            'total_views' => $totalViews, // Nouvelle variable disponible dans le template
        ]);
    }
}