<?php

namespace App\Controller;

use App\Form\UsernameType;
use App\Service\JWTService;
use App\Form\newPasswordType;
use App\Service\MailerService;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthController extends AbstractController {
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/login/i-forgot-my-password", name="app_password_forgotten")
     */
    public function newPasswordMail(JWTService $jwt, Request $request, UserRepository $ur, MailerService $mailer) {

        $form = $this->createForm(UsernameType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $username = $form->getData('username');
            $user = $ur->findOneBy(['username' => $username]);

            if ($user) {

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
                $token = str_replace('.', '*', $token);

                $mailer->send(
                    'martketplace@no-reply.com',
                    $user->getMail(),
                    'nouveau mot de passe',
                    'new-password',
                    compact('user', 'token')
                );
                $this->addFlash('success','Un mail vous a été envoyé pour modifier votre mot de passe');

            } else {
                $this->addFlash('error', 'cet utilisateur n\'existe pas');
            }
        }
        return $this->render('security/username-confirm.html.twig', [
            'usernameForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new-password/{token}", name="app_password_new")
     * Récupère l'utilisateur via le token JWT de la requête et lui permet de changer son mot de passe via un formulaire
     */
    public function newPassword($token, JWTService $jwt, UserPasswordHasherInterface $userPasswordHasher, Request $request, UserRepository $ur) {

        $token = str_replace('*', '.', $token);
        $form = $this->createForm(newPasswordType::class)->handleRequest($request);

        if ($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))) {
            //on récupère le payload
            $payload = $jwt->getPayload($token);

            //on récupère le user du token 
            $user = $ur->find($payload['user_id']);
            if ($user) {

                if ($form->isSubmitted() && $form->isValid()) {
                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user,
                            $form->get('plainPassword')->getData()
                        )
                    );
                    $ur->add($user);
                    $this->addFlash('success', 'votre mot de passe a bien été modifié');
                    return $this->redirectToRoute('app_home');
                }

                return $this->render('security/password-new.html.twig', [
                    'passwordForm' => $form->createView(),
                ]);

            } else {
                $this->addFlash('error', 'Utilisateur introuvable');
                return $this->redirectToRoute('app_home');
            }
        } else {
            $this->addFlash('error', 'Token invalide');
            return $this->redirectToRoute('app_home');
        }
    }
}
