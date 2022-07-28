<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Entity\Comment;
use App\Service\Helpers;
use App\Form\CommentType;
use App\Entity\Submission;
use App\Entity\Notification;
use App\Form\SubmissionType;
use App\Form\EditSubmissionType;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use App\Repository\CommentRepository;
use App\Repository\SubmissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\NotificationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SubmissionController extends AbstractController {
    /**
     * @Route("/post/{postId}/show", name="app_post_show")
     * Affiche un post
     */
    public function show(int $postId, SubmissionRepository $sr, Helpers $helper, Request $request, CommentRepository $cr): Response {
        $submission = $sr->find($postId);
        if (!$submission) {
                    return $helper->error(404, 'Post introuvable', 'Ce post n\'existe pas');
                }
        // on ajoute une relation entre le post et l'utilisateur pour indiquer qu'il l'a visionné 
        if($this->getUser())
            $submission->addViewedBy($this->getUser());
            
        $sr->add($submission);
        $absoluteUrl = '/images/users/' . $submission->getUrl();
        $isFaved = false;
        $editAllowed = false;
        $comment = new Comment;
        $commentForm = $this->createForm(CommentType::class, $comment)->handleRequest($request);
        if($this->getUser()==$submission->getAuthor()) $editAllowed = true;
        // permet de savoir si l'utilisateur a déjà mis ce post en favoris
        if($this->getUser() && $this->getUser()->getFavorites()->contains($submission)) $isFaved = true;
        
        // formulaire pour les commentaires sous le post
        if($commentForm->isSubmitted() && $commentForm->isValid()){
            if($helper->isVerified()){
                $comment->setUser($this->getUser());
                $comment->setDate(new \DateTime());
                $comment->setSubmission($submission);
                $cr->add($comment);

            }
            else{
                $this->addFlash('error', 'Vous devez être connecté et vérifié pour poster un commentaire');
            }
            
        }
        return $this->render('submission/show.html.twig', [
            'controller_name' => 'SubmissionController',
            'submission' => $submission,
            'isFaved'=>$isFaved,
            'editAllowed'=>$editAllowed,
            'commentForm' => $commentForm->createView(),
            'absoluteUrl'=> $absoluteUrl
        ]);
    }

    /**
     * @Route("/post/{postId}/favorite", name="app_post_fav")
     * @IsGranted("ROLE_USER")
     * Permet de rajouter un post dans les favoris d'un utilisateur
     */
    public function addFavorite(int $postId, SubmissionRepository $sr, UserRepository $ur) {
        $submission = $sr->find($postId);
        $user = $this->getUser();
        if ($submission) {
            $user->addFavorite($submission);
            $ur->add($user);
            $this->addFlash('success', 'Ce post a bien été ajouté à vos favoris');
        } else {
            $this->addFlash('error', 'Impossible d\'ajouter ce post dans vos favoris');
        }


        return $this->redirectToRoute('app_post_show', ['postId' => $postId]);
    }

    /**
     * @Route("/post/{postId}/unfavorite", name="app_post_unfav")
     * @IsGranted("ROLE_USER")
     * permet de retirer un post des favoris d'un utilisateur
     */
    public function removeFavorite(int $postId,SubmissionRepository $sr, UserRepository $ur){
        $submission = $sr->find($postId);
        $user = $this->getUser();
        if ($submission) {
            $user->removeFavorite($submission);
            $ur->add($user);
            $this->addFlash('success', 'Ce post a bien été retiré de vos favoris');
        } else {
            $this->addFlash('error', 'Impossible de retirer ce post dans vos favoris');
        }


        return $this->redirectToRoute('app_post_show', ['postId' => $postId]);
    }

    /**
     * @Route("/upload", name="app_post_upload")
     * @IsGranted("ROLE_USER")
     * Upload d'un post pour un artiste
     */
    public function upload(Request $request, TagRepository $tr, EntityManagerInterface $em, Helpers $helper) {

        if(!$helper->isVerified() || ($this->getUser()->getStatut()->getName() !== 'artiste')) return $helper->error(403, 'Erreur 403','Vous devez être un artiste vérifié pour accéder à cette page');
        
        $submission = new Submission;
        $form = $this->createForm(SubmissionType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            //les infos globales de la submission
            $submission->setAuthor($user);
            $submission->setDateCreation(new \DateTime());
            $submission->setTitle($form->get('title')->getData());
            $submission->setDescription($form->get('description')->getData());
            $submission->setCategory($form->get('category')->getData());
            $image = $form->get('image')->getData();

            //stockage de l'image
            if ($image) {
                $newName = uniqid() . '.' . $image->guessExtension();
                try {
                    $submission->setUrl($user->getUsername() . '/' . $newName);
                    $image->move(
                        $this->getParameter('app.imageDirectory') . '/' . $user->getUsername(),
                        $newName
                    );
                    $em->persist($submission);
                } catch (\Throwable $th) {
                    $this->addFlash('errors', 'un problème est survenu pendant l\'upload du fichier');
                    return $this->render('submission/upload.html.twig', [
                        'submissionForm' => $form->createView(),
                    ]);
                }
            }

            //récupération des tags sous la forme d'un tableau
            $tags = explode(' ', $form->getData()['tags']);

            //si le tag existe déjà, on ajoute la relation avec la submission, sinon on crée le tag
            foreach ($tags as $tag) {
                $DBtag = $tr->findOneBy(['name' => $tag]);
                if (!$DBtag) {
                    $tagObj = new Tag;
                    $tagObj->setName($tag);
                    $tagObj->addSubmission($submission);
                    $em->persist($tagObj);
                } else {
                    $DBtag->addSubmission($submission);
                    $em->persist($DBtag);
                }
            }
            $em->flush();

            $this->addFlash('success', 'Votre image a bien été uploadée');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('submission/upload.html.twig', [
            'submissionForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/post/{postId}/delete", name="app_post_delete")
     * @IsGranted("ROLE_USER")
     * Suppression d'un post
     */
    public function delete(int $postId, SubmissionRepository $sr, Helpers $helper, TagRepository $tr) {

        $submission = $sr->find($postId);
        if (!$sr) {
            return $helper->error(404);
        }

        if(!$helper->isAdmin($this->getUser()) && $this->getUser() != $submission->getAuthor()){
            return $helper->error(403);
        }

        $targetTags = $submission->getTags();
        $sr->remove($submission);
        //si dans la table de jointure avec les tags, les tags du post ne sont plus associés à aucun autre post, on les supprime 
        foreach ($targetTags as $tag) {
            if (count($tag->getSubmissions()) == 0)
                $tr->remove($tag);
        }

        $this->addFlash('success', 'Le post a bien été supprimé');
        return $this->redirectToRoute('app_home');
    }


    /**
     * @Route("/post/{postId}/edit", name="app_post_edit")
     * @IsGranted("ROLE_USER")
     * Permet de modifier un post
     */
    public function edit(int $postId, SubmissionRepository $sr, Helpers $helper, TagRepository $tr, Request $request, EntityManagerInterface $em, SubmissionRepository $sm) {

        $submission = $sr->find($postId);
        if (!$sr) {
            return $helper->error(404);
        }
        if($this->getUser() != $submission->getAuthor()){
            return $helper->error(403);
        }

        //on récupère les noms des tags pour pouvoir pré remplir le textarea du formulaire
        $tags = $submission->getTags();
        $tagNames = '';
        foreach ($tags as $tag) {
            $tagNames .= ' ' . $tag->getName();
        }

        $form = $this->createForm(EditSubmissionType::class, $submission)
        ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();
            $newTags = explode(' ',$form->get('tags')->getData());
            $newImage = $form->get('image')->getData();

            if ($newImage) {
                unlink($this->getParameter('app.imageDirectory') . '/' . $submission->getUrl());
                $newName = uniqid() . '.' . $newImage->guessExtension();
                try {
                    $submission->setUrl($user->getUsername() . '/' . $newName);
                    $newImage->move(
                        $this->getParameter('app.imageDirectory') . '/' . $user->getUsername(),
                        $newName
                    );
                    $em->persist($submission);
                } catch (\Throwable $th) {
                    $this->addFlash('errors', 'un problème est survenu pendant l\'upload du fichier');
                    return $this->render('submission/upload.html.twig', [
                        'submissionForm' => $form->createView(),
                    ]);
                }
            }

            foreach ($submission->getTags() as $tag) {
                $tag->removeSubmission($submission);
                $em->persist($tag);
            }
            foreach ($newTags as $tag) {
                $DBtag = $tr->findOneBy(['name' => $tag]);
                if (!$DBtag) {
                    $tagObj = new Tag;
                    $tagObj->setName($tag);
                    $tagObj->addSubmission($submission);
                    $em->persist($tagObj);
                } else {
                    $DBtag->addSubmission($submission);
                    $em->persist($DBtag);
                }
            }

            $em->flush();

            foreach ($tags as $tag) {
                if (count($tag->getSubmissions()) == 0)
                    $tr->remove($tag);
            }

            $sm->add($submission);
            $this->addFlash('success', 'Le post a bien été modifié');
            return $this->redirectToRoute('app_post_show', ['postId'=>$postId]);    

        }

        return $this->render('submission/edit.html.twig', [
            'submissionForm' => $form->createView(),
            'tagNames'=>trim($tagNames)
        ]);
    }

     /**
     * @Route("/post/{postId}/report", name="app_post_report")
     * @IsGranted("ROLE_USER")
     * Permet de signaler un post
     */
    public function report(int $postId, UserRepository $ur, SubmissionRepository $sr, NotificationRepository $nr, Request $request) {
        $submission = $sr->find($postId);
        if (!$sr) {
            $this->addFlash('error', 'Impossible de signaler ce post');
        }
        else{
            $users = $ur->findAll();
            $admins = [];
            //on teste si l'utilisateur a déjà report ce contenu
            $content = 'Le post "' . $submission->getTitle() . '" par "' .  $submission->getAuthor()->getUsername() . 
            '" a été signalé par "'. $this->getUser()->getUsername() . 
            '" comme étant contraire à nos règles d\'utitilisation <a target="_blank" class="link-anim" href="' . 
            $this->generateUrl('app_post_show', ['postId'=>$postId]) . '">Voir</a>';

            $isAlreadyReported = $nr->findOneBy(['content'=>$content]);
            if($isAlreadyReported){
                $this->addFlash('error', 'Vous avez déjà signalé ce contenu');
                return $this->redirectToRoute('app_post_show',['postId'=>$postId]);
            }

            //on récupère les admins du site pour leur envoyer des notifications
            foreach ($users as $user) {
                if(in_array('ROLE_ADMIN', $user->getRoles())){
                    $notif = new Notification;
                    $notif->setRecipient($user);
                    $notif->setAuthor(null);
                    $notif->setFromServer(true);
                    $notif->setReport(true);
                    $notif->setContent($content);
                    $nr->add($notif);
                }
                    
            }
            $this->addFlash('success', 'Nous avons enregistré votre signalement');
            
        }
        $route = $request->headers->get('referer');

        return $this->redirect($route);

    }

}
