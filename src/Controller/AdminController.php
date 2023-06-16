<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Vehicule;
use App\Form\CommandeType;
use App\Form\VehiculeType;
use App\Repository\UserRepository;
use App\Repository\CommandeRepository;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    #[Route('/admin/vehicules', name: 'admin_vehicules')]
    #[Route('/admin/edit/{id}', name: 'admin_edit')]
    public function gestion(Request $request, EntityManagerInterface $manager, VehiculeRepository $repo, Vehicule $vehicule = null) :Response
    {     
        $voitures = $repo->findAll();

        if($vehicule == null) 
        {
            $vehicule = new vehicule;        
        }
        
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            $vehicule->setTitre($vehicule->getMarque() . " " . $vehicule->getModele());
            $vehicule->setDateEnregistrement(new \DateTime);
            $manager->persist($vehicule);
            $manager->flush();
            $this->addFlash('success', 'Les données ont été bien enregistrées');
            return $this->redirectToRoute('admin_vehicules');
        }
        
        return $this->render('admin/index.html.twig', [
            'vehicules' => $voitures,
            'form' => $form,
            'editMode' => $vehicule->getId() != null,
        ]);
    }

    
    #[Route('/admin/delete/{id}', name: 'admin_delete')]
    public function delete(EntityManagerInterface $manager, Vehicule $vehicule) {
        $manager->remove($vehicule);
        $manager->flush();
        $this->addFlash('warning', 'Les données ont été bien suprimées');
        return $this->redirectToRoute('admin_vehicules');
        
    }    

    #[Route('/admin/show/{id}', name: 'admin_show')]
    public function show(Vehicule $vehicule) :Response
    {   
        return $this->render('admin/show.html.twig', [
            'vehicule' => $vehicule
        ]);
    }

    #[Route('admin/users', name: 'admin_users')]
    public function user(Request $request, EntityManagerInterface $manager, UserRepository $repo, User $user = null) {
        $users = $repo->findAll();

        if($user == null) {
            $user = new User;
        }

        $formUser = $this->createForm(UserType::class, $user);
        $formUser->handleRequest($request);
        if($formUser->isSubmitted() && $formUser->isValid()) {
            $user->setDateEnregistrement(new \DateTime);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('success', 'Utilisateur enregistré');
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/gestionUser.html.twig', [
            'users' => $users,
            'form' => $formUser,
            'editMode' => $user->getId() != null,
        ]);

        return $this->render("admin/gestionUser.html.twig", [
            'users' => $users
        ]);
    }

    #[Route('admin/user/edit{id}', name: 'admin_user_edit')]
    public function userEdit(User $user) 
    {
        if(in_array("ROLE_ADMIN", $user->getRoles())) {
            $role = "ROLE_ADMIN";
        } 
        return $this->render ('admin/modifyUser.html.twig',['role' => $role] ) ;
    }

    #[Route('admin/buy', name: 'admin_buy')]
    public function buy(Request $request, EntityManagerInterface $manager, CommandeRepository $repo, Commande $commande = null, Vehicule $vehicule, User $user) {
        $commandes = $repo->findAll();

        if ($commande == null) {
            $commande = new commande;
        }

        $formCommande = $this->createForm(CommandeType::class, $commande);
        $formCommande->handleRequest($request);

        
        if($formCommande->isSubmitted() && $formCommande->isValid()) {

            $dateDepart = $commande->getDateDepart();
            $dateFin = $commande->getDateHeureFin();
            $prix = $vehicule->getPrixJournalier();
            $prixTotal = ($dateDepart - $dateFin) * $prix;
            
            $commande->setDateEnregistrement(new \DateTime)
                    ->setPrixTotal($prixTotal)
                    ->setUser($user)
                    ->setVehicule($this->getId() . " - " . $this->getTitre());
        }



        return $this->render('admin/gestionCommandes.html.twig');

    }









}
