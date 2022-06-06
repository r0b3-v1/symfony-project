<?php

namespace App\Controller;

use App\Repository\SubmissionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="app_home")
     */
    public function index(SubmissionRepository $sr): Response
    {
        $submissions = $sr->findAll();
        return $this->render('home/home.html.twig', [
            'controller_name' => 'HomeController',
            'submissions'=>$submissions
        ]);
    }

    /**
     * @Route("/terms", name="app_terms")
     */
    public function terms() : Response {
        return $this->render('home/terms.html.twig');
    }
}
