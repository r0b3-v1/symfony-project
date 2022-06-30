<?php

namespace App\Controller;

use App\Repository\SubmissionRepository;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    /**
     * @Route("/home/search/{page}", name="app_search")
     */
    public function search(Request $request, int $page = 1,TagRepository $tr, SubmissionRepository $sr) : Response {
        $paramsGet = $request->query->get('params');
        $submissions = [];
        $totalPage = 1;
        //dans ce cas c'est une recherche rapide donc peu complexe
        if($paramsGet){

            $submissions = $sr->search($paramsGet);
    
            $tags = $tr->findBy(['name'=>$paramsGet]);
            foreach ($tags as $tag) {
                $submissions = array_merge($submissions, $tag->getSubmissions()->toArray());
            }

            $postPerPage = 20;
            $totalPage = ceil(count($submissions)/$postPerPage);
        }
        //dans ce cas c'est une recherche avancée faite via le formulaire de recherche
        else{

        }


        $submissions = array_unique($submissions);
        return $this->render('home/home.html.twig', [
            'controller_name' => 'HomeController',
            'submissions'=>$submissions,
            'page'=>$page,
            'totalPage'=>$totalPage,
            'paramsGet'=>$paramsGet
        ]);
    }

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
