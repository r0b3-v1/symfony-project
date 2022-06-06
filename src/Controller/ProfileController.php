<?php

namespace App\Controller;

use App\Entity\Submission;
use App\Entity\Tag;
use App\Form\SubmissionType;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use App\Service\Helpers;
use Doctrine\ORM\EntityManagerInterface;
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
        $currentUser = $this->getUser();
        if (!$user) {
            return $helper->error(404, 'Utilisateur introuvable', 'Cet utilisateur n\'existe pas!');
        }
        if ($currentUser && $currentUser->getId() === $user->getId())
            $allowEditing = true;

        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user,
            'allowEditing' => $allowEditing
        ]);
    }

    /**
     * @Route("/upload", name="test_image")
     */
    public function uploadSubmission(Request $request, TagRepository $tr, EntityManagerInterface $em) {
        $submission = new Submission;
        $form = $this->createForm(SubmissionType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $submission->setAuthor($user);
            $submission->setDateCreation(new \DateTime());
            $submission->setTitle($form->get('title')->getData());
            $submission->setDescription($form->get('description')->getData());
            $image = $form->get('image')->getData();
            if($image){
                $ok = true;
                $newName = uniqid().'.'.$image->guessExtension();
                try {
                    $submission->setUrl($user->getUsername().'/'.$newName);
                    $image->move(
                        $this->getParameter('app.imageDirectory').'/'.$user->getUsername(),
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
            $tags = explode(' ',$form->getData()['tags']);


            foreach ($tags as $tag) {
                $DBtag = $tr->findOneBy(['name'=>$tag]);
                if(!$DBtag){
                    $tagObj = new Tag;
                    $tagObj->setName($tag);
                    $tagObj->addSubmission($submission);
                    $em->persist($tagObj);
                }
                else{
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
}
