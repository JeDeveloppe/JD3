<?php

namespace App\Controller\Site;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CvController extends AbstractController
{
    #[Route('/cv-manager', name: 'app_cv')]
    public function index(): Response
    {
        return $this->render('cv/cv-manager.html.twig', [
            'controller_name' => 'CvController',
        ]);
    }
}
