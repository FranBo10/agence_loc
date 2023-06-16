<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Commande;
use App\Entity\Vehicule;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/vehicule/{id}', name: 'show')]
    public function show(Request $request, EntityManagerInterface $manager, Vehicule $vehicule) {   
        
        $commande = new Commande;


        $formCommande = $this->createForm(CommandeType::class, $commande);
        $formCommande->handleRequest($request);

        if($formCommande->isSubmitted() && $formCommande->isValid()) {
        
            $dateDepart = $commande->getDateHeureDepart();
            $dateFin = $commande->getDateHeureFin();
            $prix = $vehicule->getPrixJournalier();
            $diff = $dateFin->diff($dateDepart);
            $days = $diff->days;
            $prixTotal = $days * $prix;

            $commande->setDateEnregistrement(new \DateTime)
                    ->setPrixTotal($prixTotal)
                    ->setUser($this->getUser())
                    ->setVehicule($vehicule);
            $manager->persist($commande);
            $manager->flush();
            $this->addFlash('success', 'Commande enregistré');
            return $this->redirectToRoute('home');
        }       
        

    return $this->render('app/show.html.twig', [
        'vehicule' => $vehicule,
         'form' => $formCommande,
         'commandes' => $commande
    ]);
    }

    




   
}
