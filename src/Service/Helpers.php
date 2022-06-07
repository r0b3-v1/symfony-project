<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Helpers extends AbstractController{

    public function error(int $code, string $customTitle ='', string $customMessage=''){
        return $this->render('bundles/TwigBundle/Exception/error'.$code.'.html.twig', [
            'customTitle' => $customTitle,
            'customMessage' => $customMessage
        ]);
    }
}