<?php

namespace App\Controller;

use App\Repository\VehiculeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(VehiculeRepository $repo): Response
    {
        $vehicules = $repo->findAll();

        return $this->render('app/index.html.twig', [
            'vehicules' => $vehicules
        ]);
    }

   
}
