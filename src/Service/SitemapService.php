<?php

namespace App\Service;

use App\Repository\CategoryRepository;
use App\Repository\ArticleRepository;
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
        private ArticleRepository $articleRepository
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
        
        //?on recupere toutes les routes du projet
        $allRoutesGeneratedByControllers = $this->routerInterface->getRouteCollection()->all();

        //?on boucle sur les routes
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
        $categories = $this->categoryRepository->findAll();
        foreach($categories as $category){
            $urls[] = [                
                'loc'        => $this->routerInterface->generate('mapped_my_articles_from_category', ['categorySlug' => $category->getSlug()]),
                'lastmod'    => $category->getUpdatedAt()->format('Y-m-d'),
                'changefreq' => "monthly",
                'priority'   => 0.8
            ];
        }

        $articles = $this->articleRepository->findBy(['isOnline' => true ]);
        foreach($articles as $article){
            $urls[] = [
                'loc'        => $this->routerInterface->generate('mapped_article_details', ['categorySlug' => $article->getCategory()->getSlug(), 'articleSlug' => $article->getSlug()]),
                'lastmod'    => $category->getUpdatedAt()->format('Y-m-d'),
                'changefreq' => "monthly",
                'priority'   => 0.8
            ];
        }

        $arrays['urls'] = $urls;

        return $arrays;
    }

}