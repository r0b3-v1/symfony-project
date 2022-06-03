<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use App\Service\JWTService;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController {
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager, MailerService $mailerService, JWTService $jWTService): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setIsVerified(false);

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            //on génère le JWT de l'utilisateur
            //on crée le header
            $header = [
                'alg' => 'HS256',
                'typ' => 'JWT'
            ];

            //on crée le payload
            $payload = [
                'user_id' => $user->getId()
            ];

            //on génère le token
            $token = $jWTService->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            //on remplace les . car ils provoquent une erreur dans l'url
            $token = str_replace('.','*',$token);


            //on envoie un mail
            $mailerService->send(
                'no-reply@martketplace.com',
                $user->getMail(),
                'Activation de votre compte MarketPlace',
                'register',
                compact('user', 'token')
            );

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verif/{token}", name="verify_user", requirements={"token"=".+"})
     */
    public function verifyUser($token, JWTService $jwtService, UserRepository $userRepository, EntityManagerInterface $em): Response {
        
        //on retransforme les * en .
        $token = str_replace('*','.',$token);

        //on vérifie si le token est valide, n'a pas expiré et n'a pas été modifié
        if ($jwtService->isValid($token) && !$jwtService->isExpired($token) && $jwtService->check($token, $this->getParameter('app.jwtsecret'))) {
            //on récupère le payload
            $payload = $jwtService->getPayload($token);

            //on récupère le user du token 
            $user = $userRepository->find($payload['user_id']);

            //on vérifie que l'utilisateur existe et n'a pas encore activé son compte
            if ($user && !$user->getIsVerified()) {
                $user->setIsVerified(true);
                $em->flush($user);
                $this->addFlash('success', 'Utilisateur activé');
                return $this->redirectToRoute('app_home');
            }
        }
        //ici un problème se pose dans le token
        $this->addFlash('danger', 'Le token est invalide ou a expiré');
        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/renvoiverif", name="resend_verif")
     */
    public function resendVerif(JWTService $jwt, MailerService $mailer, UserRepository $userRepository) {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('danger', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_login');
        }
        if ($user->getIsVerified()) {
            $this->addFlash('warning', 'Cet utilisateur est déjà activé');
            return $this->redirectToRoute('app_home');
        }

        //on génère le JWT de l'utilisateur
        //on crée le header
        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT'
        ];

        //on crée le payload
        $payload = [
            'user_id' => $user->getId()
        ];

        //on génère le token
        $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));


        //on envoie un mail
        $mailer->send(
            'no-reply@martketplace.com',
            $user->getMail(),
            'Activation de votre compte MarketPlace',
            'register',
            compact('user', 'token')
        );

        $this->addFlash('success','Email de vérification renvoyé');
        return $this->redirectToRoute('app_home');

    }
}
