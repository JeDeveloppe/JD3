<?php

namespace App\Controller\Site;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Form\ContactType;
use App\Service\MailerService;
use App\Service\NinjasApiService;
use App\Service\OpenWeatherService;
use App\Repository\ArticleRepository;
use App\Repository\ProjectRepository;
use App\Repository\CategoryRepository;
use App\Repository\TrainingRepository;
use App\Service\MentionsLegalesService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\LegalInformationRepository;
use App\Repository\TechnologyFamilyRepository;
use App\Service\ChartService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SiteController extends AbstractController
{
    public function __construct(
        private ProjectRepository $projectRepository,
        private CategoryRepository $categoryRepository,
        private PaginatorInterface $paginator,
        private LegalInformationRepository $legalInformationRepository,
        private MentionsLegalesService $mentionsLegalesService,
        private ArticleRepository $articleRepository,
        private TechnologyFamilyRepository $technologyFamilyRepository,
        private TrainingRepository $trainingRepository,
        private MailerService $mailerService,
        private OpenWeatherService $openWeatherService,
        private NinjasApiService $ninjasApiService,
        private ChartService $chartService
    )
    {}

    #[Route('/', name: 'site_home')]
    public function index(): Response
    {
        $metas['description'] = ''; //TODO

        $citiesDatas = [];
        $citiesDatas[] = $this->openWeatherService->getWeatherFromOneCity("Caen");
        $citiesDatas[] = $this->openWeatherService->getWeatherFromOneCity("Strasbourg");

        return $this->render('site/pages/home/index.html.twig', [
            'h1' => 'Développeur web full stack',
            'citiesDatas' => $citiesDatas,
            'metas' => $metas
        ]);
    }

    #[Route('/contact', name: 'site_contact')]
    public function contact(Request $request): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
        $metas['description'] = 'Si vous avez la moindre question, n\'hésitez pas à m\'envoyer un message !';

        if($form->isSubmitted() && $form->isValid()) {
    
            $this->mailerService->sendMailFromContactPage(
                "Message du site concernant: ".$form->get('sujet')->getData(),
                [
                    'mail' => $form->get('email')->getData(),
                    'question' => $form->get('message')->getData()
                ]
            );
    
            $this->addFlash('success', 'Message bien envoyé!');
            return $this->redirectToRoute('site_contact');
        }

        return $this->render('site/pages/contact/contact.html.twig', [
            'h1' => 'Contactez- moi !',
            'form' => $form->createView(),
            'metas' => $metas
        ]);
    }

    #[Route('/mes-realisations', name: 'site_my_projects')]
    public function myProjects(Request $request): Response
    {
        $donneesFromDatabases = $this->projectRepository->findBy(['isOnline' => true], ['startedAt' => 'DESC']);

        $projects = $this->paginator->paginate(
            $donneesFromDatabases, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            8 /*limit per page*/
        );

        $metas['description'] = 'Une pleine collection de mes projets en cours ou terminés.';

        return $this->render('site/pages/projects/projects.html.twig', [
            'projects' => $projects,
            'h1' => 'Mes réalisations',
            'metas' => $metas
        ]);
    }

    #[Route('/mes-formations', name: 'site_my_trainings')]
    public function myTrainings(Request $request): Response
    {
        $donneesFromDatabases = $this->trainingRepository->findBy(['isOnline' => true], ['startedAt' => 'DESC']);

        $trainings = $this->paginator->paginate(
            $donneesFromDatabases, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            8 /*limit per page*/
        );

        $metas['description'] = ''; //TODO

        return $this->render('site/pages/trainings/trainings.html.twig', [
            'trainings' => $trainings,
            'h1' => 'Mes formations',
            'metas' => $metas
        ]);
    }

    #[Route('/blog', name: 'site_my_categories')]
    public function myCategories(Request $request): Response
    {
        $donneesFromDatabases = $this->categoryRepository->findAll();

        $categories = $this->paginator->paginate(
            $donneesFromDatabases, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            12 /*limit per page*/
        );

        $metas['description'] = 'Mon petit google à moi des actuces, programmes, conseils trouvez sur le web pour mes réalisations.';

        //pour chaque categorie,je cherche le dernier article
        $lastArticles = [];
        foreach ($categories as $category) {
            $lastArticles[$category->getName()] = $this->articleRepository->findOneBy(['category' => $category, 'isOnline' => true ], ['id' => 'DESC']) ?? null;
        }

        return $this->render('site/pages/categories/categories.html.twig', [
            'categories' => $categories,
            'lastArticles' => $lastArticles,
            'h1' => 'Mes astuces, conseils et programmes',
            'lead' => 'Pour ne plus chercher sur google ! <i class="fa-regular fa-face-laugh-wink"></i>',
            'metas' => $metas
        ]);
    }

    #[Route('/blog/{categorySlug}', name: 'mapped_my_articles_from_category')]
    public function myArticles(Request $request,$categorySlug): Response
    {
        $category = $this->categoryRepository->findOneBy(['slug' => $categorySlug]);

        if (!$category) {
            throw $this->createNotFoundException('La catégorie n\'existe pas.');
        }

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
            'h1' => 'Mes articles: '.$category->getName(),
            'metas' => $metas,
            'form' => $form
        ]);
    }

    #[Route('/blog/{categorySlug}/{articleSlug}', name: 'mapped_article_details')]
    public function articleDetails($categorySlug, $articleSlug): Response
    {
        $category = $this->categoryRepository->findOneBy(['slug' => $categorySlug]);

        if (!$category) {
            throw $this->createNotFoundException('La catégorie n\'existe pas.');
        }

        $article = $this->articleRepository->findOneBy(['slug' => $articleSlug]);

        if (!$article) {
            throw $this->createNotFoundException('Cet article n\'existe pas.');
        }

        $metas['description'] = ''; //TODO

        return $this->render('site/pages/articles/article.html.twig', [
            'category' => $category,
            'h1' => $article->getName(),
            'article' => $article,
            'metas' => $metas
        ]);
    }

    #[Route('/mentions-legales', name: 'site_mentions_legales')]
    public function mentionsLegales(): Response
    {
        $legales = $this->legalInformationRepository->findOneBy([]);
        $paragraphs = $this->mentionsLegalesService->mentionsParagraphs($legales);
        $metas['description'] = 'Mentions légales du site.';

        return $this->render('site/pages/legale/mentions_legales.html.twig', [
            'legales' => $legales,
            'metas' => $metas,
            'paragraphs' => $paragraphs
        ]);
    }

    #[Route('/mes-connaissances', name: 'site_my_knowledges')]
    public function myKnowledges(): Response
    {
        $technologiesFamilies = $this->technologyFamilyRepository->findBy([], ['orderOfAppearance' => 'ASC']);

        $chart = $this->chartService->generateRadarChart($technologiesFamilies);


        $metas['title'] = 'Mes connaissances';

        $metas['description'] = '';   //TODO


        return $this->render('site/pages/knowledges/knowledges.html.twig', [
            'technologiesFamilies' => $technologiesFamilies,
            'h1' => 'Mes connaissances',
            'metas' => $metas,
            'chart' => $chart
        ]);
    }
}
