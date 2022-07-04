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

    //calcule l'intersection entre deux tableaux d'entités
    public function intersectArrays(array $arr1, array $arr2) : array {
        $result = [];
        foreach ($arr1 as $e1) {
            foreach ($arr2 as $e2) {
                if($e1->getId() == $e2->getId())
                    $result[] = $e1;
            }
        }
        return $result;
    }

    //supprime le dossier et tous son contenu récursivement
    public static function deleteDir($dirPath) {
        if (! is_dir($dirPath)) {
            return;
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