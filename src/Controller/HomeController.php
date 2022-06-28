<?php

namespace App\Controller;

use App\Repository\SubmissionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/home/{page}", name="app_home")
     */
    public function index(int $page = 1, SubmissionRepository $sr): Response
    {
        // $submissions = $sr->findAll();
        $postPerPage = 20;
        $totalPage = ceil(count($sr->findAll())/$postPerPage);
        $submissions = $sr->findBy([],null,$postPerPage, ($page-1)*$postPerPage);
        return $this->render('home/home.html.twig', [
            'controller_name' => 'HomeController',
            'submissions'=>$submissions,
            'page'=>$page,
            'totalPage'=>$totalPage
        ]);
    }

    /**
     * @Route("/terms", name="app_terms")
     */
    public function terms() : Response {
        return $this->render('home/terms.html.twig');
    }
}
