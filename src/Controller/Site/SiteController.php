<?php

namespace App\Controller\Site;

use App\Form\ContactType;
use App\Repository\ProjectRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SiteController extends AbstractController
{
    public function __construct(
        private ProjectRepository $projectRepository,
        private PaginatorInterface $paginator
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
}
