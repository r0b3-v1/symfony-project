<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Helpers extends AbstractController{

    public function error(int $code, string $customTitle ='', string $customMessage=''){
        return $this->render('bundles/TwigBundle/Exception/error'.$code.'.html.twig', [
            'customTitle' => $customTitle,
            'customMessage' => $customMessage
        ]);
    }

    public function checkUser(string $username){
        return $this->getUser() && ($this->getUser()->getUsername() === $username);
    }
}