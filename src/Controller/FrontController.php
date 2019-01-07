<?php

namespace App\Controller;

use App\Entity\Unavailability;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    /**
     * Page d'accueil de l'application.
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('front/index.html.twig');
    }
}
