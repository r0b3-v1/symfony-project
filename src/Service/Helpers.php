<?php

namespace App\Service;

use App\Entity\User;
use InvalidArgumentException;
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


    public function isAdmin(User $user){
        return in_array('ROLE_ADMIN', $user->getRoles());
    }

    //supprime le dossier et tous son contenu récursivement
    public static function deleteDir($dirPath) {
        if (! is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);

        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }
}