<?php

namespace App\Controller\Site;

use App\Entity\Category;
use App\Entity\Domain;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\DomainRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/blog/{domainSlug}', name: 'blog_mapped_')]
class BlogController extends AbstractController
{

    private string $domainSlug;

    public function __construct(
        private CategoryRepository $categoryRepository,
        private ArticleRepository $articleRepository,
        private PaginatorInterface $paginator,
        private DomainRepository $domainRepository,
    )
    {}

    #[Route('/{categorySlug}', name: 'my_articles_from_category')]
    public function myArticles(Request $request,$categorySlug): Response
    {
        $category = $this->controleIfDomainAndCategoryExist($request->attributes->get('domainSlug'), $categorySlug);

        $articles = $this->articleRepository->findBy(['category' => $category, 'isOnline' => true ], ['id' => 'DESC']);

        $metas['description'] = 'Mon petit google à moi des actuces, programmes, conseils trouvez sur le web pour mes réalisations concernant la catégorie '.$category->getName();

        $form = $this->createForm(ArticleType::class);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {

            $search = str_replace(" ","%", $form->get('search')->getData());

            $articles = $this->articleRepository->findArticleByName($search, $category);

        }

        return $this->render('site/pages/articles/articles.html.twig', [
            'category' => $category,
            'articles' => $articles,
            'domain' => $category->getDomain(),
            'h1' => 'Mes articles: '.$category->getName(),
            'metas' => $metas,
            'form' => $form
        ]);
    }

    #[Route('/blog/informatique/{categorySlug}/{articleSlug}', name: 'article_details')]
    public function articleDetails(Request $request, string $categorySlug, string $articleSlug): Response
    {
        $category = $this->controleIfDomainAndCategoryExist($request->attributes->get('domainSlug'), $categorySlug);

        $article = $this->articleRepository->findOneBy(['slug' => $articleSlug]);

        if (!$article) {
            throw $this->createNotFoundException('Cet article n\'existe pas.');
        }

        $metas['description'] = ''; //TODO

        return $this->render('site/pages/articles/article.html.twig', [
            'category' => $category,
            'domain' => $category->getDomain(),
            'h1' => $article->getName(),
            'article' => $article,
            'metas' => $metas
        ]);
    }

    public function controleIfDomainAndCategoryExist(string $domainName, string $categorySlug): Category
    {
        $domain = $this->domainRepository->findOneBy(['name' => $domainName]);

        if (!$domain) {
            throw $this->createNotFoundException('Le domaine n\'existe pas.');
        }

        $category = $this->categoryRepository->findOneBy(['slug' => $categorySlug, 'domain' => $domain]);

        if (!$category) {
            throw $this->createNotFoundException('La catégorie n\'existe pas.');
        }

        return $category;
    }

}
