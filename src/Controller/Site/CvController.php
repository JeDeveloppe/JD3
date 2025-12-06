<?php

namespace App\Controller\Site;

use Dompdf\Dompdf;
use Dompdf\Options;
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
            'is_pdf_export' => false,
        ]);
    }

    #[Route('/cv/download/{cvName}', name: 'cv_download_pdf')]
    public function downloadCvInPdf(string $cvName): Response
    {
        // 1. Vérification : On regarde si la clé $cvName existe dans le tableau
        if (!isset(self::AUTHORIZED_CVS[$cvName])) {
            $this->addFlash('error', 'Le CV demandé n\'existe pas.');
            return $this->redirectToRoute('app_cv'); 
        }

        // 1. Configuration de Dompdf (Options pour l'encodage et le rendu)
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isHtml5ParserEnabled', true);
        $pdfOptions->set('isRemoteEnabled', true); // Important si vous avez des images externes ou CDN
        
        $dompdf = new Dompdf($pdfOptions);

        // 2. Générer le contenu HTML du CV
        // (Assurez-vous que les chemins de vos assets CSS sont absolus pour Dompdf !)
        $html = $this->renderView('cv/'.$cvName.'.html.twig', [
            // Passez ici les données dynamiques de votre CV si nécessaire
            'is_pdf_export' => true,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // 3. Renvoyer le PDF comme réponse téléchargeable
        $filename = 'CV_RENE_WETTA_' . $cvName . '.pdf';

        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            ]
        );
    }
}