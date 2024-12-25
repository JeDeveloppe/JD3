<?php

namespace App\Service;

use App\Repository\CategoryRepository;
use App\Repository\TrickRepository;
use DateTimeZone;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class SitemapService
{
    public function __construct(
        private RequestStack $requestStack,
        private RouterInterface $routerInterface,
        private CategoryRepository $categoryRepository,
        private TrickRepository $trickRepository
        ){
    }

    public function generateRoutes(): array
    {
        //?on retournera le tout dans un mega tableau
        $arrays = [];
        
        //?on recupere l'hôte
        $arrays['hostname'] = $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost();

        //?il nous faut un tableau des différents urls
        $urls = [];

        //?on recupere la date au moment de la requete
        $now = new DateTimeImmutable('now', new DateTimeZone('Europe/Paris'));
        
        $allRoutesGeneratedByControllers = $this->routerInterface->getRouteCollection()->all();

        foreach($allRoutesGeneratedByControllers as $key => $route){
            //! les routes sans paramêtres commencent par site_
            if(substr($key,0,5) == 'site_'){
                //? on met dans le tableau les différentes route
                $urls[] = [
                    'loc'        => $this->routerInterface->generate($key),
                    'lastmod'    => $now->format('Y-m-d'),
                    'changefreq' => "monthly", //monthly,daily
                    'priority'   => 0.8
                    ];
            }
        }     

        //! les autres routes un peu plus complexes
        // #[Route('/mes-astuces/{categoryId}/{categorySlug}', name: 'mapped_my_tricks')]
        $categories = $this->categoryRepository->findAll();
        foreach($categories as $category){
            $urls[] = [                
                'loc'        => $this->routerInterface->generate('mapped_my_tricks', ['categoryId' => $category->getId(), 'categorySlug' => $category->getSlug()]),
                'lastmod'    => $category->getUpdatedAt()->format('Y-m-d'),
                'changefreq' => "monthly",
                'priority'   => 0.8
            ];
        }
        // #[Route('/mes-astuces/{categoryId}/{categorySlug}/{trickId}/{trickSlug}', name: 'mapped_trick_details')]
        $tricks = $this->trickRepository->findBy(['isOnline' => true ]);

        foreach($tricks as $trick){
            $urls[] = [
                'loc'        => $this->routerInterface->generate('mapped_trick_details', ['categoryId' => $trick->getCategory()->getId(), 'categorySlug' => $trick->getCategory()->getSlug(), 'trickId' => $trick->getId(), 'trickSlug' => $trick->getSlug()]),
                'lastmod'    => $category->getUpdatedAt()->format('Y-m-d'),
                'changefreq' => "monthly",
                'priority'   => 0.8
            ];
        }

        $arrays['urls'] = $urls;

        return $arrays;
    }

}