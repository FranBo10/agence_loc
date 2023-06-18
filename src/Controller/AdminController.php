<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Commande;
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
    public function gestion(Request $request, EntityManagerInterface $manager, VehiculeRepository $repo, Vehicule $vehicule = null): Response
    {
        $vehicules = $repo->findAll();

        if ($vehicule == null) {
            $vehicule = new vehicule;
        }

        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $vehicule->setTitre($vehicule->getMarque() . " " . $vehicule->getModele());
            $vehicule->setDateEnregistrement(new \DateTime);
            $manager->persist($vehicule);
            $manager->flush();
            $this->addFlash('success', 'Les données ont été bien enregistrées');
            return $this->redirectToRoute('admin_vehicules');
        }

        return $this->render('admin/index.html.twig', [
            'vehicules' => $vehicules,
            'form' => $form,
            'editMode' => $vehicule->getId() != null,
        ]);
    }


    #[Route('/admin/delete/{id}', name: 'admin_delete')]
    public function delete(EntityManagerInterface $manager, Vehicule $vehicule)
    {
        $manager->remove($vehicule);
        $manager->flush();
        $this->addFlash('warning', 'Les données ont été bien suprimées');
        return $this->redirectToRoute('admin_vehicules');
    }

    #[Route('/admin/show/{id}', name: 'admin_show')]
    public function show(Vehicule $vehicule): Response
    {
        return $this->render('admin/show.html.twig', [
            'vehicule' => $vehicule
        ]);
    }

    #[Route('/admin/users', name: 'admin_users')]
    public function user(Request $request, EntityManagerInterface $manager, UserRepository $repo, User $user = null)
    {
        $users = $repo->findAll();

        if ($user == null) {
            $user = new User;
        }

        $formUser = $this->createForm(UserType::class, $user);
        $formUser->handleRequest($request);

        if ($formUser->isSubmitted() && $formUser->isValid()) {
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
    }

    #[Route('/admin/user/edit{id}', name: 'admin_user_edit')]
    public function userEdit(User $user)
    {
        if (in_array("ROLE_ADMIN", $user->getRoles())) {
            $role = "ROLE_ADMIN";
        }
        return $this->render('admin/modifyUser.html.twig', ['role' => $role]);
    }


    #[Route('/admin/commandes', name: 'commandes')]
    public function commande(Request $request, EntityManagerInterface $manager, CommandeRepository $repo, Commande $commande, Vehicule $vehicule, User $user)
    {
        $commandes = $repo->findAll();

        if ($commande = null) {
            $commande = new commande;
        }

        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = new User;
            $vehicule = new Vehicule;

            $dateDepart = $commande->getDateHeureDepart();
            $dateFin = $commande->getDateHeureFin();
            $prix = $vehicule->getPrixJournalier();
            $diff = $dateFin->diff($dateDepart);
            $days = $diff->days;
            $prixTotal = $days * $prix;

            $commande->setPrixTotal($prixTotal)
                ->setDateEnregistrement(new \DateTime)
                ->setUser($user)
                ->setVehicule($vehicule);
            $manager->persist($commande);
            $manager->flush();
            $this->addFlash('success', 'Commande enregistré');
            return $this->redirectToRoute('commandes');
        }

        return $this->render('admin/gestionCommandes.html.twig', [
            'commandes' => $commandes,
            'form' => $form,
            'vehicule' => $vehicule,
            'user' => $user

        ]);
    }

    #[Route('admin/commandes/delete/{id}', name: 'commande_delete')]
    public function deleteCommande(EntityManagerInterface $manager, Commande $commande)
    {
        $manager->remove($commande);
        $manager->flush();
        $this->addFlash("success", "La commande a été supprimée");
        return $this->redirectToRoute('commandes');
    }
}
