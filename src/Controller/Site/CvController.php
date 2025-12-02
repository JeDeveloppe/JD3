<?php

namespace App\Controller\Site;

use App\Service\CvService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class CvController extends AbstractController
{
    /**
     * @var array
     * Tableau privé contenant toutes les métadonnées des CV :
     * - 'file': Le nom du fichier Twig.
     * - 'title': Le titre utilisé sur le bouton de la page d'index.
     */
    private const AUTHORIZED_CVS = [
        'manager' => [
            'file' => 'manager.html.twig',
            'title' => 'Voir le CV Manager Opérationnel',
            'description' => 'CV de Manager Opérationnel et Commercial avec expertise en gestion multi-sites et leadership d\'équipes transversales.',
            'icon' => 'bi-people',
            'color' => 'btn-info'
        ]
    ];

    #[Route('/cv', name: 'app_cv')]
    public function index(): Response
    {
        // 1. On passe le tableau complet des CV autorisés à la vue d'index
        return $this->render('cv/index.html.twig', [
            'cv_list' => self::AUTHORIZED_CVS,
        ]);
    }

    /**
     * Gère l'affichage d'un CV dynamique et le comptage des vues.
     */
    #[Route('/cv/{cvName}', name: 'app_cv_show')]
    public function cvShow(string $cvName, CvService $cvService): Response
    {
        // 1. Vérification : On regarde si la clé $cvName existe dans le tableau
        if (!isset(self::AUTHORIZED_CVS[$cvName])) {
            $this->addFlash('error', 'Le CV demandé n\'existe pas.');
            return $this->redirectToRoute('app_cv'); 
        }

        // 2. Récupération des données spécifiques
        $cvData = self::AUTHORIZED_CVS[$cvName];
        $templatePath = 'cv/' . $cvData['file']; 

        // 3. Gestion des vues
        $totalViews = $cvService->returnNumberOfViewsAndAddNewView($cvName);
        
        // 4. Affichage du template
        return $this->render($templatePath, [
            'controller_name' => 'CvController',
            'cv_name' => $cvName,
            'total_views' => $totalViews,
        ]);
    }
}