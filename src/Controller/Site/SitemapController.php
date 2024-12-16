<?php

namespace App\Controller\Site;

use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class SitemapController extends AbstractController
{
    public function __construct(
        private SluggerInterface $slugger,
        private RouterInterface $routerInterface
        )
    {
    }

    #[Route('/sitemap.xml', name: 'site_sitemap')]
    public function index(Request $request): Response
    {

        //tableau vide
        $urls = [];
        $now = new DateTimeImmutable('now');
        $hostname = $request->getSchemeAndHttpHost();

        $collection = $this->routerInterface->getRouteCollection();
        $allRoutes = $collection->all();

        foreach($allRoutes as $key => $route){
            //! important toutes les routes pour le sitemap doivent commencer par mapped_ sauf les catalogues traités après
            if(substr($key,0,7) == 'mapped_'){
                //? on met dans le tableau les différentes route
                $urls[] = [
                    'loc'        => $this->generateUrl($key),
                    'lastmod'    => $now->format('Y-m-d'),
                    'changefreq' => "monthly", //monthly,daily
                    'priority'   => 0.8
                    ];
            }
        }      
        //! traitement des catalogues
        //?exemple a garder
        // $occasions = $this->occasionRepository->findBy(['isOnline' => true]);

        // foreach($occasions as $occasion){
        //     $urls[] = [                
        //         'loc'        => $this->generateUrl('occasion', ['reference_occasion' => $occasion->getReference(), 'editor_slug' => $occasion->getBoite()->getEditor()->getSlug() ?? "VIDE", 'boite_slug' => strtolower($occasion->getBoite()->getSlug() ?? "VIDE") ]),
        //         'lastmod'    => $occasion->getBoite()->getCreatedAt()->format('Y-m-d'),
        //         'changefreq' => "monthly",
        //         'priority'   => 0.8
        //     ];
        // }

        $response = new Response(
            $this->renderView('site/sitemap/sitemap.html.twig', [
                'urls'     => $urls,
                'hostname' => $hostname
            ]),
            200
        );

        $response->headers->set('Content-type', 'text/xml');
        
        return $response;
    }
}
