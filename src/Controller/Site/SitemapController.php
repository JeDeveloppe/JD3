<?php

namespace App\Controller\Site;

use App\Service\SitemapService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class SitemapController extends AbstractController
{
    public function __construct(
        private SitemapService $sitemapService,
        )
    {}

    #[Route('/sitemap.xml', name: 'sitemap', defaults: ['_format' => 'xml'])]
    public function index(): Response
    {

        $arrays = $this->sitemapService->generateRoutes();

        $response = new Response(
            $this->renderView('site/sitemap/sitemap.html.twig', [
                'urls'     => $arrays['urls'],
                'hostname' => $arrays['hostname'],
            ]),
            200
        );

        $response->headers->set('Content-type', 'text/xml');
        
        return $response;
    }
}
