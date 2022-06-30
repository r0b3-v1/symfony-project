<?php

namespace App\Controller;

use App\Form\UserType;
use App\Repository\StatutRepository;
use App\Repository\UserRepository;
use App\Service\Helpers;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
        $user = $ur->findOneBy(['username' => $username]);
        if (!$user) {
            return $helper->error(404, 'Utilisateur introuvable', 'Cet utilisateur n\'existe pas!');
        }
        $allowEditing = $helper->checkUser($username);

        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user,
            'allowEditing' => $allowEditing
        ]);
    }

    /**
     * @Route("/infos/status/change", name="app_change_status")
     */
    public function changeStatus(string $username, UserRepository $ur, StatutRepository $sr, Helpers $helper){
        if (!$helper->checkUser($username)){
            $this->addFlash('error', 'Vous n\'avez pas l\'autorisation de modifier ce profil');
        }
        else{
            $user = $ur->findOneBy(['username' => $username]);
            $statut = $sr->findOneBy(['name' => 'artiste']);
            $user->setStatut($statut);
            $ur->add($user);
            $this->addFlash('success', 'Vous êtes maintenant un artiste! Il y a de nouvelles sections que vous pouvez remplir dans votre profil!');
        }
        return $this->redirectToRoute("app_profile_infos", ['username'=>$username]);
    }

    /**
     * @Route("/infos/edit", name="app_profile_edit_infos")
     */
    public function edit(string $username, Request $request, UserRepository $ur, Helpers $helper) {
        $user = $ur->findOneBy(['username' => $username]);
        if($user != $this->getUser()){
            return $helper->error(403);
        }
        $previousAvatar = $ur->find($user->getId())->getAvatar();

        $form = $this->createForm(UserType::class, $user)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            

            $avatar = $form->get('avatar')->getData();

            //stockage de l'image
            if ($avatar) {
                $newName = 'avatar.' . $avatar->guessExtension();
                try {
                    $user->setAvatar($user->getUsername() . '/' . $newName);
                    $avatar->move(
                        $this->getParameter('app.imageDirectory') . '/' . $user->getUsername(),
                        $newName
                    );
                } catch (\Throwable $th) {
                    $this->addFlash('errors', 'un problème est survenu pendant l\'upload de l\'avatar');
                    return $this->render('profile/edit/infos.html.twig', [
                        'profileForm' => $form->createView(),
                        'user' => $user
                    ]);
                }
            }
            else{
                $user->setAvatar($previousAvatar);
            }
            $ur->add($user);

            return $this->redirectToRoute('app_profile_infos', ['username' => $user->getUsername()]);
        }

        return $this->render('profile/edit/infos.html.twig', [
            'profileForm' => $form->createView(),
            'user' => $user
        ]);
    }
}
