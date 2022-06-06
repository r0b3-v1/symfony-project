<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubmissionController extends AbstractController
{
    #[Route('/submission', name: 'app_submission')]
    public function index(): Response
    {
        return $this->render('submission/index.html.twig', [
            'controller_name' => 'SubmissionController',
        ]);
    }
}
