<?php

namespace App\Controller\Site;

use App\Form\TrickType;
use App\Form\ContactType;
use App\Repository\TrickRepository;
use App\Repository\ProjectRepository;
use App\Repository\CategoryRepository;
use App\Service\MentionsLegalesService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\LegalInformationRepository;
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
        private TrickRepository $trickRepository,
    )
    {}

    #[Route('/', name: 'site_home')]
    public function index(): Response
    {
        $metas['description'] = ''; //TODO

        return $this->render('site/pages/home/index.html.twig', [
            'controller_name' => 'SiteController',
        ]);
    }

    #[Route('/contact', name: 'site_contact')]
    public function contact(Request $request): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
        $metas['description'] = 'Si vous avez la moindre question, n\'hésitez pas à m\'envoyer un message !';

        if($form->isSubmitted() && $form->isValid()) {
    

            $this->mailService->sendMail(
                true,
                $legales->getEmailCompany(),
                "Message du site en date du ".(new DateTimeImmutable('now'))->format('d-m-Y').": ".$form->get('sujet')->getData(),
                'contact',
                [
                    'mail' => $form->get('email')->getData(),
                    'question' => $form->get('message')->getData(),
                    'legales' => $legales
                ],
                $form->get('email')->getData(),
                false
            );
    
            $this->addFlash('success', 'Message bien envoyé!');
            return $this->redirectToRoute('app_contact');
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
        $donneesFromDatabases = $this->projectRepository->findAll();

        $projects = $this->paginator->paginate(
            $donneesFromDatabases, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            8 /*limit per page*/
        );

        $metas['description'] = 'Une pleine collection de mes projets en cours ou terminés.';

        return $this->render('site/pages/projects/projects.html.twig', [
            'projects' => $projects,
            'h1' => 'Mes Realisations',
            'metas' => $metas
        ]);
    }

    #[Route('/mes-astuces-par-categorie', name: 'site_my_categories')]
    public function myCategories(Request $request): Response
    {
        $donneesFromDatabases = $this->categoryRepository->findAll();

        $categories = $this->paginator->paginate(
            $donneesFromDatabases, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            12 /*limit per page*/
        );

        $metas['description'] = 'Mon petit google à moi des actuces, programmes, conseils trouvez sur le web pour mes réalisations.';

        return $this->render('site/pages/categories/categories.html.twig', [
            'categories' => $categories,
            'h1' => 'Mes astuces par catégorie',
            'lead' => 'Pour ne plus chercher sur google ! <i class="fa-regular fa-face-laugh-wink"></i>',
            'metas' => $metas
        ]);
    }

    #[Route('/mes-astuces-pour-la-categorie/{id}/{slug}', name: 'site_my_tricks')]
    public function myTricks(Request $request,$id, $slug): Response
    {
        $category = $this->categoryRepository->findOneBy(['id' => $id, 'slug' => $slug]);

        if (!$category) {
            throw $this->createNotFoundException('La catégorie n\'existe pas.');
        }

        $tricks = $this->trickRepository->findBy(['category' => $category], ['name' => 'ASC']);

        $metas['description'] = 'Mon petit google à moi des actuces, programmes, conseils trouvez sur le web pour mes réalisations concernant la catégorie '.$category->getName();

        $form = $this->createForm(TrickType::class);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {

            $search = str_replace(" ","%", $form->get('search')->getData());

            $tricks = $this->trickRepository->findTrickByName($search, $category);

        }

        return $this->render('site/pages/tricks/tricks.html.twig', [
            'category' => $category,
            'tricks' => $tricks,
            'h1' => 'Mes astuces '.$category->getName(),
            'metas' => $metas,
            'form' => $form
        ]);
    }

    #[Route('/mes-astuces-pour-la-categorie/{categoryId}/{categorySlug}/{trickId}/{trickSlug}', name: 'site_trick_details')]
    public function trickDetails($categoryId, $categorySlug, $trickId, $trickSlug): Response
    {
        $category = $this->categoryRepository->findOneBy(['id' => $categoryId, 'slug' => $categorySlug]);

        if (!$category) {
            throw $this->createNotFoundException('La catégorie n\'existe pas.');
        }

        $trick = $this->trickRepository->findOneBy(['id' => $trickId, 'slug' => $trickSlug]);

        if (!$trick) {
            throw $this->createNotFoundException('Cette astuce n\'existe pas.');
        }

        $metas['description'] = ''; //TODO

        return $this->render('site/pages/tricks/trick.html.twig', [
            'category' => $category,
            'h1' => $trick->getName(),
            'trick' => $trick,
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
}
