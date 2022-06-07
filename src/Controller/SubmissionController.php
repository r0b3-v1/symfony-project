<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Service\Helpers;
use App\Entity\Submission;
use App\Form\SubmissionType;
use App\Repository\TagRepository;
use App\Repository\SubmissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SubmissionController extends AbstractController {
    /**
     * @Route("/post/{postId}/show", name="app_post_show")
     */
    public function show(int $postId, SubmissionRepository $sr, Helpers $helper): Response {
        $submission = $sr->find($postId);
        if (!$submission) {
            return $helper->error(404, 'Post introuvable', 'Ce post n\'existe pas');
        }
        return $this->render('submission/show.html.twig', [
            'controller_name' => 'SubmissionController',
            'submission' => $submission
        ]);
    }


    /**
     * @Route("/upload", name="app_post_upload")
     */
    public function upload(Request $request, TagRepository $tr, EntityManagerInterface $em, Helpers $helper) {
        if(!$this->getUser()){
            return $helper->error(404,'Accès non autorisé','Vous ne pouvez pas upload de fichier sans être connecté');
        }
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
     */
    public function delete(int $postId, SubmissionRepository $sr, Helpers $helper, TagRepository $tr){
        
        $submission = $sr->find($postId);
        if(!$sr){
            return $helper->error(404);
        }

        $targetTags = $submission->getTags();

        $sr->remove($submission);
        //si dans la table de jointure avec les tags, les tags du post ne sont plus associés à aucun autre post, on les supprime 
        foreach ($targetTags as $tag) {
            if(count($tag->getSubmissions()) == 0)
                $tr->remove($tag);
        }
        
        $this->addFlash('success','Le post a bien été supprimé');
        return $this->redirectToRoute('app_home');
    }
}