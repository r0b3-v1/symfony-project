<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\Helpers;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user/{username}")
 */
class ProfileController extends AbstractController {
    /**
     * @Route("/infos", name="app_profile_infos")
     */
    public function index(string $username, Helpers $helper, UserRepository $ur): Response {
        $allowEditing = false;
        $user = $ur->findOneBy(['username'=>$username]);
        $currentUser = $this->getUser();
        if(!$user){
            return $helper->error(404, 'Utilisateur introuvable', 'Cet utilisateur n\'existe pas!');
        }
        if($currentUser && $currentUser->getId()===$user->getId())
            $allowEditing = true;
            
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'user'=>$user,
            'allowEditing'=>$allowEditing
        ]);
    }

    /**
     * @Route("/edit", name="test_image")
     */
    public function edit(){
        

    }
}
