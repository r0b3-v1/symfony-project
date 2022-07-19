<?php

namespace App\Controller;

use App\Service\Helpers;
use App\Entity\Commission;
use App\Entity\Notification;
use App\Form\CommissionType;
use App\Repository\UserRepository;
use App\Repository\CommissionRepository;
use App\Repository\NotificationRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CommissionStatutRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_USER")
 */
class CommissionController extends AbstractController
{
    /**
     * @Route("/{username}/commission", name="app_commission")
     * Crée la demande de commission via un formulaire rempli par le client
     */
    public function commission(string $username, Request $request, UserRepository $ur, NotificationRepository $nr, Helpers $helper, CommissionRepository $cr, CommissionStatutRepository $csr): Response
    {
        if(!$helper->isVerified()) return $helper->error(403, 'Erreur 403','Vous devez être vérifié pour accéder à cette page');

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
     * Annule la demande de commission si celle ci est encore en attente
     */
    public function cancel($commissionId, CommissionRepository $cr, NotificationRepository $nr, CommissionStatutRepository $csr, Helpers $helper, Request $request){
        $commission = $cr->find($commissionId);
        $route = $request->headers->get('referer');
        if(!$commission){
            return $helper->error(404);
        }
        if($commission->getStatut()->getName() != 'en attente'){
            $this->addFlash('error', 'Impossible d\'annuler une commande qui a été validée');
            return $this->redirect($route);
        }

        $artist = $commission->getArtist();
        $client = $commission->getClient();

        $statut = $csr->findOneBy(['name'=>'annulé']);
        $commission->setStatut($statut);

        $notif = new Notification();
        
        // dans ce cas c'est le client qui a annulé la commande, on le notifie
        if($client->getId() == $this->getUser()->getId()){
            $notif->setAuthor($client);
            $notif->setRecipient($artist);  
            $notif->setContent('Le client "' . $client->getUsername() . '" a annulé sa commande "'.$commission->getTitle() . '"');
            $this->addFlash('success', 'Votre demande a bien été annulée');
        }
        //sinon c'est l'artiste, on le notifie également
        else{
            $notif->setAuthor($artist);
            $notif->setRecipient($client);  
            $notif->setContent('L\'artiste "' . $artist->getUsername() . '" a refusé votre commande "'.$commission->getTitle() . '"');
            $this->addFlash('success', 'La commande a bien été annulée');
        }
        $nr->add($notif);

        $cr->add($commission);
        

        return $this->redirect($route);

    }

    /**
     * @Route("/commission/{commissionId}/accept", name="app_commission_accept")
     * L'artiste valide la commande, on récupère la requête et on s'assure que le prix est bien un flottant
     */
    public function accept($commissionId, CommissionRepository $cr, NotificationRepository $nr, CommissionStatutRepository $csr, Helpers $helper, Request $request){
        $commission = $cr->find($commissionId);
        $route = $request->headers->get('referer');
        $testFloat = '/^([0-9]*[.])?[0-9]+$/';
        if(!$commission){
            return $helper->error(404);
        }
        $artist = $commission->getArtist();
        $client = $commission->getClient();

        $price = $request->request->get('price');

        if($price && preg_match($testFloat, $price)){
            $price = floatval($price);
            $statut = $csr->findOneBy(['name'=>'en cours']);
            $commission->setStatut($statut);
            $commission->setPrice($price);

            //on notifie le client que la commande est acceptée
            $notif = new Notification();
            $notif->setAuthor($artist);
            $notif->setRecipient($client);
            $notif->setContent('Votre demande "' . $commission->getTitle() . '" a été acceptée par "' . $artist->getUsername() . '"');
            $nr->add($notif);

            $cr->add($commission);
            $this->addFlash('success', 'La demande a été acceptée');
        }
        else{
            $this->addFlash('error', 'Erreur, la demande n\'a pas pu être validée');
        }
        
        return $this->redirect($route);

    }

    /**
     * @Route("/commission/{commissionId}/done", name="app_commission_done")
     * L'artiste passe la commande au stade terminé
     */
    public function done($commissionId, CommissionRepository $cr, NotificationRepository $nr, CommissionStatutRepository $csr, Helpers $helper, Request $request){
        $commission = $cr->find($commissionId);
        $route = $request->headers->get('referer');
        if(!$commission){
            return $helper->error(404);
        }
        $artist = $commission->getArtist();
        $client = $commission->getClient();

        $statut = $csr->findOneBy(['name'=>'terminé']);
        $commission->setStatut($statut);

        //on notifie le client que la commande est terminée
        $notif = new Notification();
        $notif->setAuthor($artist);
        $notif->setRecipient($client);
        $notif->setContent('Votre demande "' . $commission->getTitle() . '" est terminée');
        $nr->add($notif);

        $cr->add($commission);
        $this->addFlash('success', 'La commande est terminée');
        
        return $this->redirect($route);

    }


}
