<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\Helpers;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="app_admin")
     * page d'accueil pour les admins
     */
    public function index(UserRepository $ur): Response
    {
        $users = $ur->findAll();
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'users'=>$users
        ]);
    }

    /**
     * @Route("/admin/delete/{userId}", name="app_admin_delete")
     * Permet de supprimer un utilisateur
     */
    public function deleteUser(UserRepository $ur, int $userId, Helpers $helper): Response
    {
        $user = $ur->find($userId);
        $helper->deleteDir($this->getParameter('app.imageDirectory').'/'.$user->getUsername());
        $ur->remove($user);
        $this->addFlash('success', 'L\'utilisateur a été supprimé');

        return $this->redirectToRoute('app_admin');
    }
}
