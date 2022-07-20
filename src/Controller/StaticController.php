<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StaticController extends AbstractController
{

    /**
     * @Route("/terms", name="app_terms")
     */
    public function terms() : Response {
        return $this->render('static/terms.html.twig');
    }
}
