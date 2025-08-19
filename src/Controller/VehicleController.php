<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Entity\User;
use App\Form\VehicleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/vehicle')]
final class VehicleController extends AbstractController
{
    #[Route('/', name: 'app_vehicle_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Accès refusé. Veuillez vous connecter.');
        }

        $vehicle = new Vehicle();
        $form = $this->createForm(VehicleType::class, $vehicle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $vehicle->setUser($user);
            $entityManager->persist($vehicle);
            $entityManager->flush();
            $this->addFlash('success', 'Votre véhicule a été ajouté avec succès !');

            return $this->redirectToRoute('app_user_profile', ['_fragment' => 'vehicles']);
        }

        $userVehicles = $user->getVehicles();

        return $this->render('profile/vehicules.html.twig', [
            'addVehiculeForm' => $form->createView(),
            'vehicles' => $userVehicles,
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_vehicle_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Vehicle $vehicle, EntityManagerInterface $entityManager): Response
    {
        if ($vehicle->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier ce véhicule.');
        }
        
        $form = $this->createForm(VehicleType::class, $vehicle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Votre véhicule a été mis à jour avec succès !'); 

            return $this->redirectToRoute('app_user_profile', ['_fragment' => 'vehicles']);
        }

        return $this->render('vehicle/edit.html.twig', [
            'vehicle' => $vehicle,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_vehicle_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Vehicle $vehicle, EntityManagerInterface $entityManagerInterface): Response
    {
        if ($vehicle->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer ce véhicule.');
        }

        // Vérification jeton CSRF
        if ($this->isCsrfTokenValid('delete' . $vehicle->getId(), $request->request->get('_token'))) {
            $entityManagerInterface->remove($vehicle);
            $entityManagerInterface->flush();
            $this->addFlash('success', 'Véhicule supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Token de sécurité invalide. La suppression a échoué.');
        }

        return $this->redirectToRoute('app_user_profile', ['_fragment' => 'vehicles']);
    }
}