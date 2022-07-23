<?php

namespace App\Controller;

use App\Repository\TagRepository;
use App\Repository\UserRepository;
use App\Repository\CategoryRepository;
use App\Repository\SubmissionRepository;
use App\Service\Helpers;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{

    /**
     * @Route("/home/search/{page}", name="app_search")
     * Fonction de recherche appelée à la fois dans le cas de la recherche rapide et de la recherche avancée
     */
    public function search(Request $request, int $page = 1,Helpers $helper, TagRepository $tr, SubmissionRepository $sr, CategoryRepository $cr, UserRepository $ur) : Response {
        $paramsGet = $request->query->get('params');
        $paramsPost = $request->request;
        $arrayPost = [];
        $categories = $cr->findAll();
        $submissions = [];
        $totalPage = 1;
        $users = [];
        $type = 'post';
        //dans ce cas c'est une recherche rapide donc peu complexe
        if(!is_null($paramsGet)){
            
            $submissions = $sr->quickSearch($paramsGet);
            
            $tags = $tr->findBy(['name'=>$paramsGet]);
            foreach ($tags as $tag) {
                $submissions = array_merge($submissions, $tag->getSubmissions()->toArray());
            }

            $postPerPage = $this->getParameter('app.postPerPage');
            $totalPage = ceil(count($submissions)/$postPerPage);
        }

        //dans ce cas c'est une recherche avancée faite via le formulaire de recherche
        elseif (count($paramsPost->all())>0){
            
            $type = $paramsPost->get('type');
            $arrayPost['type'] = $type;

            // dans ce cas on cherche des images
            if($type == 'post'){
                
                $title = $paramsPost->get('title');
                $arrayPost['title'] = $title;
                $description = $paramsPost->get('description');
                $arrayPost['description'] = $description;
                $authorName = $paramsPost->get('author');
                if($authorName)
                    $authorId = -1;
                else
                    $authorId = null;
                $arrayPost['author'] = $authorName;
                $author = $ur->findOneBy(['username'=>$authorName]);
                if($author) $authorId = $author->getId();
    
                    

                $tags = $paramsPost->get('tags');
                //pour récupérer les posts avec les tags concernés
                $sub = [];
                if($tags){
                    $arrayPost['tags'] = $tags;
                    $tags = explode(' ', $tags);
                    foreach ($tags as $tagName) {
                        $tag = $tr->findOneBy(['name'=>$tagName]);
                        if($tag){
                            $sub = array_merge($sub, $tag->getSubmissions()->toArray());
                        }
                    }
                    $sub = array_unique($sub);
                }

                $categoryId = $paramsPost->get('categories');
                $arrayPost['categoryId'] = $categoryId;
                if($categoryId=='all') $categoryId = null;
                $submissions = $sr->search(['title'=>$title, 'description'=>$description, 'author'=>$authorId, 'categoryId'=>$categoryId]);

                if($tags){
                    $submissions = $helper->intersectArrays($submissions, $sub);
                }

            }
            // sinon on cherche des utilisateurs
            else{

                $name = $paramsPost->get('name');
                $arrayPost['title'] = $name;
                $dispo = $paramsPost->get('dispo');
                $arrayPost['dispo'] = $dispo;
                $option = $paramsPost->get('sort-artist');
                $arrayPost['option'] = $option;

                $users = $ur->search(['name'=>$name, 'dispo'=>$dispo, 'option'=>$option]);
            }

        }

        // élimination des éventuels doublons
        $submissions = array_unique($submissions);
        return $this->render('home/home.html.twig', [
            'submissions'=>$submissions,
            'page'=>$page,
            'totalPage'=>$totalPage,
            'paramsGet'=>$paramsGet,
            'arrayPost'=>$arrayPost,
            'categories'=>$categories,
            'users'=>$users,
            'request'=>$request
        ]);
    }

    /**
     * @Route("/home/{page}", name="app_home")
     */
    public function index(int $page = 1, SubmissionRepository $sr, CategoryRepository $cr): Response
    {
        // $submissions = $sr->findAll();^
        $categories = $cr->findAll();
        $postPerPage = $this->getParameter('app.postPerPage');
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

}
