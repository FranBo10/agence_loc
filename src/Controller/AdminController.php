<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Form\VehiculeType;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    #[Route('/admin/gestion', name: 'admin_gestion')]
    public function gestion(VehiculeRepository $repo) :Response
    {
        $vehicules = $repo->findAll();
        return $this->render('admin/gestion.html.twig', [
            'vehicules' => $vehicules
        ]);
    }

    #[Route('/admin/add', name: 'admin_add')]
    #[Route('/admin/edit/{id}', name: 'admin_edit')]
    public function add(Request $request, EntityManagerInterface $manager, Vehicule $vehicule = null) :Response {

        if($vehicule == null) 
        {
            $vehicule = new vehicule;        
        }

        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);
        
        $modele = $request->request->get('modele');
        if($form->isSubmitted() && $form->isValid()) {
            $marque = $request->request->get('marque');
            $vehicule->setTitre($vehicule->getMarque() . " " . $vehicule->getModele());
            $vehicule->setDateEnregistrement(new \DateTime);
            $manager->persist($vehicule);
            $manager->flush();
            $this->addFlash('success', 'Les données ont été bien enregistrées');
            return $this->redirectToRoute('admin_gestion');
        }

        return $this->render('admin/formVehicule.html.twig', [
            'form' => $form,
            'editMode' => $vehicule->getId()
        ]);
    }

    #[Route('/admin/delete/{id}', name: 'admin_delete')]
    public function delete(EntityManagerInterface $manager, Vehicule $vehicule) {
        $manager->remove($vehicule);
        $manager->flush();
        $this->addFlash('warning', 'Les données ont été bien suprimées');
        return $this->redirectToRoute('admin_gestion');
        
    }    

    #[Route('/app/show/{id}', name: 'app_show')]
    public function show(VehiculeType $repo, EntityManagerInterface $manager, Vehicule $vehicule) :Response
    {
        $vehicules = $repo->find(['id' => $vehicule->getId()]);
        $repo->handleRequest();


    }
}
