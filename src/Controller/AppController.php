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
    public function show(Request $request, EntityManagerInterface $manager, Vehicule $vehicule, Commande $commande = null)
    {
        if ($commande == null) {
            $commande = new Commande;
        }
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        $dateDepart = $commande->getDateHeureDepart();
        $dateFin = $commande->getDateHeureFin();
        $prix = $vehicule->getPrixJournalier();

        if ($dateDepart !== null && $dateFin !== null) {
            $diff = $dateFin->diff($dateDepart);
            $days = $diff->days;
            $prixTotal = $days * $prix;
        } else {
            // Manejar el caso en el que una o ambas variables sean nulas
            $prixTotal = 0; // O cualquier valor predeterminado que desees
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $commande->setId($commande->getId())
                ->setDateEnregistrement(new \DateTime)
                ->setPrixTotal($prixTotal)
                ->setUser($this->getUser())
                ->setVehicule($vehicule);
            $manager->persist($commande);
            $manager->flush();
            $this->addFlash('success', 'Commande enregistré');
            return $this->redirectToRoute('devis', ['id'=> $commande->getId()]);
        }

        return $this->render('app/show.html.twig', [
            'vehicule' => $vehicule,
            'form' => $form,
            'commandes' => $commande,
            'prixTotal' => $prixTotal,
            'id' => $commande->getId()
        ]);
    }


    #[Route('/devis/{id}', name: 'devis')]
    public function devis(CommandeRepository $repo, Commande $commande)
    {
        $id = $commande->getId();
        $commande = $repo->find($id);

        return $this->render('app/devis.html.twig', [
            'commande' => $commande

        ]);
    }

    // #[Route('/devis/{id}', name: 'devis_total')]
    // public function totalDevis(Request $request, EntityManagerInterface $manager, Commande $commande)
    // {
    //     retun $this->render('app/devis')


    // }




}
