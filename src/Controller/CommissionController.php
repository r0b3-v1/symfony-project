<?php

namespace App\Controller;

use App\Entity\Commission;
use App\Entity\Notification;
use App\Form\CommissionType;
use App\Repository\CommissionRepository;
use App\Repository\CommissionStatutRepository;
use App\Repository\NotificationRepository;
use App\Service\Helpers;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommissionController extends AbstractController
{
    /**
     * @Route("/{username}/commission", name="app_commission")
     */
    public function commission(string $username, Request $request, UserRepository $ur, NotificationRepository $nr, Helpers $helper, CommissionRepository $cr, CommissionStatutRepository $csr): Response
    {
        $user = $ur->findOneBy(['username' => $username]);
        
        //si l'utilisateur n'existe pas ou que ce n'est pas un artiste alors on renvoie une erreur 404
        if(!$user || $user->getStatut()->getName() !='artiste'){
            return $helper->error(404);
        }

        $defaultStatut = $csr->findOneBy(['name'=>'en attente']);
        $commission = new Commission();
        $form = $this->createForm(CommissionType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $title = $form->get('title')->getData();
            $deadline = $form->get('deadline')->getData();
            $description = $form->get('description')->getData();
            $noDeadline = $form->get('nodeadline')->getData();
            $category = $form->get('category')->getData();
            $client = $this->getUser();
            $artist = $user;

            if(!$noDeadline) $commission->setDeadline($deadline);

            $commission->setTitle($title);
            $commission->setDescription($description);
            $commission->setCategory($category);
            $commission->setClient($client);
            $commission->setArtist($artist);
            $commission->setStatut($defaultStatut);
            $commission->setPrice(0);
            
            $cr->add($commission);

            $notif = new Notification;
            $notif->setAuthor($client);
            $notif->setRecipient($artist);
            $notif->setContent('Nouvelle demande de commission');
            $notif->setSeen(false);

            $nr->add($notif);
            $this->addFlash('success', 'Votre demande a bien été enregistrée');
            

            return $this->redirectToRoute('app_profile_infos', ['username' => $user->getUsername()]);
        }

        return $this->render('commission/form.html.twig', [
            'commissionForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/commission/{commissionId}/cancel", name="app_commission_cancel")
     */
    public function cancel($commissionId, CommissionRepository $cr, CommissionStatutRepository $csr, Helpers $helper, Request $request){
        $commission = $cr->find($commissionId);
        if(!$commission){
            return $helper->error(404);
        }

        $statut = $csr->findOneBy(['name'=>'annulé']);
        $commission->setStatut($statut);

        $cr->add($commission);
        $this->addFlash('success', 'Votre demande a bien été annulée');
        $route = $request->headers->get('referer');

        return $this->redirect($route);

    }
}
