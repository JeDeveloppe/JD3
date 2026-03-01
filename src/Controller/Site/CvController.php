<?php

namespace App\Controller\Site;

use Mpdf\Mpdf;
use App\Service\CvService;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/liste-des-cv', name: 'cv_liste', methods: ['GET'])]
    public function index(): Response
    {
        $listCv = self::AUTHORIZED_CVS;

        // 1. On passe le tableau complet des CV autorisés à la vue d'index
        return $this->render('cv/index.html.twig', [
            'cv_list' => self::AUTHORIZED_CVS,
        ]);
    }

    /**
     * Gère l'affichage d'un CV dynamique et le comptage des vues.
     */
    #[Route('/cv/{cvName}', name: 'cv_show', requirements: ['cvName' => '.+'])]
    public function cvShow(string $cvName, CvService $cvService, Request $request): Response
    {
        // 1. Vérification : On regarde si la clé $cvName existe dans le tableau
        if (!isset(self::AUTHORIZED_CVS[$cvName])) {
            $this->addFlash('error', 'Le CV demandé n\'existe pas.');
            return $this->redirectToRoute('cv_liste'); 
        }

        // 2. Récupération des données spécifiques
        $cvData = self::AUTHORIZED_CVS[$cvName];
        $templatePath = 'cv/' . $cvData['file']; 

        // 3. Gestion des vues
        $totalViews = $cvService->returnNumberOfViewsAndAddNewView($cvName);

        $isPdfExport = $request->query->has('pdf');
        
        // 4. Affichage du template
        return $this->render($templatePath, [
            'controller_name' => 'CvController',
            'cv_name' => $cvName,
            'total_views' => $totalViews,
            'is_pdf_export' => $isPdfExport,
        ]);
    }

}