<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommissionController extends AbstractController
{
    /**
     * @Route("/commission", name="app_commission")
     */
    public function index(): Response
    {
        return $this->render('commission/index.html.twig', [
            'controller_name' => 'CommissionController',
        ]);
    }
}
