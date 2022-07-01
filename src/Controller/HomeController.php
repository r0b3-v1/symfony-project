<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\SubmissionRepository;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    /**
     * @Route("/home/search/{page}", name="app_search")
     */
    public function search(Request $request, int $page = 1,TagRepository $tr, SubmissionRepository $sr, CategoryRepository $cr, UserRepository $ur) : Response {
        $paramsGet = $request->query->get('params');
        $categories = $cr->findAll();
        $submissions = [];
        $totalPage = 1;
        //dans ce cas c'est une recherche rapide donc peu complexe
        if($paramsGet){

            $submissions = $sr->quickSearch($paramsGet);

    
            $tags = $tr->findBy(['name'=>$paramsGet]);
            foreach ($tags as $tag) {
                $submissions = array_merge($submissions, $tag->getSubmissions()->toArray());
            }

            $postPerPage = 20;
            $totalPage = ceil(count($submissions)/$postPerPage);
        }
        //dans ce cas c'est une recherche avancée faite via le formulaire de recherche
        else{
            $paramsPost = $request->request;
            $title = $paramsPost->get('title');
            $description = $paramsPost->get('description');
            $author = $paramsPost->get('author');
            $authorId = $ur->findOneBy(['username'=>$author]);
            $tags = $paramsPost->get('tags');
            $categoryId = $paramsPost->get('categories');
            if($categoryId=='all') $categoryId = null;
            


            dump($sr->search(['title'=>$title, 'description'=>$description, 'authorId'=>$authorId, 'categoryId'=>$categoryId]));


        }


        $submissions = array_unique($submissions);
        return $this->render('home/home.html.twig', [
            'controller_name' => 'HomeController',
            'submissions'=>$submissions,
            'page'=>$page,
            'totalPage'=>$totalPage,
            'paramsGet'=>$paramsGet,
            'categories'=>$categories
        ]);
    }

    /**
     * @Route("/home/{page}", name="app_home")
     */
    public function index(int $page = 1, SubmissionRepository $sr, CategoryRepository $cr): Response
    {
        // $submissions = $sr->findAll();^
        $categories = $cr->findAll();
        $postPerPage = 20;
        $totalPage = ceil(count($sr->findAll())/$postPerPage);
        $submissions = $sr->findBy([],null,$postPerPage, ($page-1)*$postPerPage);
        return $this->render('home/home.html.twig', [
            'controller_name' => 'HomeController',
            'submissions'=>$submissions,
            'page'=>$page,
            'totalPage'=>$totalPage,
            'categories'=>$categories
        ]);
    }

    

    /**
     * @Route("/terms", name="app_terms")
     */
    public function terms() : Response {
        return $this->render('home/terms.html.twig');
    }
}
