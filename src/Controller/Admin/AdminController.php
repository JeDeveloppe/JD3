<?php

namespace App\Controller\Admin;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    public function __construct(
    )
    {}

    #[Route('/admin/write-text', name: 'admin_write_texts')]
    public function adminWriteTexts(): Response
    {

        return $this->render('admin/_writeTexts.html.twig', []);
    }

}
