<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\Helpers;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user")
 */
class ProfileController extends AbstractController {
    /**
     * @Route("/{username}", name="app_profile")
     */
    public function index(string $username, Helpers $helper, UserRepository $ur): Response {
        $user = $ur->findOneBy(['username'=>$username]);
        if(!$user){
            return $helper->error(404, 'Utilisateur introuvable', 'Cet utilisateur n\'existe pas!');
        }
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'user'=>$user
        ]);
    }
}
