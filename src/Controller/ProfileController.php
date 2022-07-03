<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Form\NotifType;
use App\Form\UserType;
use App\Repository\CommissionStatutRepository;
use App\Repository\NotificationRepository;
use App\Repository\StatutRepository;
use App\Repository\UserRepository;
use App\Service\Helpers;
use DateTimeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ProfileController extends AbstractController {
    /**
     * @Route("/user/{username}/infos", name="app_profile_infos")
     */
    public function index(string $username, Helpers $helper, UserRepository $ur, CommissionStatutRepository $csr): Response {
        $allowEditing = false;
        $statuts = $csr->findAll(); 
        $user = $ur->findOneBy(['username' => $username]);
        if (!$user) {
            return $helper->error(404, 'Utilisateur introuvable', 'Cet utilisateur n\'existe pas!');
        }
        $allowEditing = $helper->checkUser($username);

        $notifs = $user->getReceivedNotifs();
        $senders = [];
        foreach ($notifs as $notif) {
            $sender = $notif->getAuthor()->getUsername();
            if(!in_array($sender,$senders)) $senders[] = $sender;
        }
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user,
            'allowEditing' => $allowEditing,
            'senders' => $senders,
            'statuts' => $statuts
        ]);
    }

    /**
     * @Route("/user/{username}/infos/status/change", name="app_change_status")
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
     * @Route("/user/{username}/infos/edit", name="app_profile_edit_infos")
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

    /**
     * @Route("/user/{username}/notif", name="app_send_notif")
     */
    public function sendNotif(string $username, UserRepository $ur, Helpers $helper, Request $request, NotificationRepository $nr){
        $recipient = $ur->findOneBy(['username' => $username]);
        if(!$recipient){
            return $helper->error(404);
        }
        $author = $this->getUser();
        if(!$author){
            return $helper->error(403);
        }

        $form = $this->createForm(NotifType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $notif = new Notification;
            $notif->setAuthor($author);
            $notif->setRecipient($recipient);
            $notif->setContent($form->get('content')->getData());
            $notif->setSeen(false);

            $nr->add($notif);
            $this->addFlash('success', 'Votre notification a bien été envoyée');

            return $this->redirectToRoute('app_profile_infos',['username'=>$username]);

        }

        

        return $this->render('profile/notification.html.twig', [
            'notifForm' => $form->createView(),
            'recipient' => $recipient
        ]);

    }

    public function listUnseenNotifs(){
        $user = $this->getUser();
        $unseen = 0;

        if($user) {
            $notifs = $user->getReceivedNotifs();
            foreach ($notifs as $notif) {
                if(!$notif->getSeen()) $unseen ++;
            }
        }

        return $this->render('_partials/unseenNotifs.html.twig', ['notifs' => $unseen]);

    }

    /**
     * @Route("/user/{username}/update-notif", name="app_update_notifs")
     */
    public function updateNotifs(string $username, NotificationRepository $nr, UserRepository $ur){
        $user = $ur->findOneBy(['username'=>$username]);
        if($user){
            $notifs = $user->getReceivedNotifs();
            foreach ($notifs as $notif) {
                $notif->setSeen(true);
                $nr->add($notif);
            }
        }
        return new Response();
    }

}
