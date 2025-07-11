<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Vehicle;
use App\Form\UserProfileFormType;
use App\Form\UserProfilePictureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Form\UserPreferenceType;
use App\Form\VehicleType;
use App\Form\UserAllPreferencesFormType;
use App\Form\UserVehiclesCollectionType;

final class UserController extends AbstractController
{
    #[Route('/profile', name: 'app_user_profile')]
    #[IsGranted('ROLE_USER')]
    public function profile(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();//Récupére l'utilisateur connecté

        // Traitement du formulaire
        $profileForm = $this->createForm(UserProfileFormType::class, $user);
        $profileForm->handleRequest($request);

        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Votre profil a été mis à jour.');
            return $this->redirectToRoute('app_user_profile');
        }

        // Crée le formulaire pour la photo de profil
        $profilePictureForm = $this->createForm(UserProfilePictureType::class, $user);
        $profilePictureForm->handleRequest($request);

        if ($profilePictureForm->isSubmitted() && $profilePictureForm->isValid()) {
            // VichUpload gère automatiquement l'upload de la photo de profil
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre photo de profil a été mise à jour avec succès.');

            return $this->redirectToRoute('app_user_profile');
        }

        $preferencesForm = $this->createForm(UserPreferenceType::class, $user);
        $preferencesForm->handleRequest($request);

        if ($preferencesForm->isSubmitted() && $preferencesForm->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Vos préférences ont été mises à jour !');
            return $this->redirectToRoute('app_user_profile');
        }

        $profileForm = $this->createForm(UserProfileFormType::class, $user);
        $profileForm->handleRequest($request);

         if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Vos informations personnelles ont été mises à jour !');
            return $this->redirectToRoute('app_user_profile');
        }

        $vehiclesForm = $this->createForm(UserVehiclesCollectionType::class, $user);
        $vehiclesForm->handleRequest($request);

        if ($vehiclesForm->isSubmitted() && $vehiclesForm->isValid()) {
            if ($user->isDriver() && $user->getVehicles()->isEmpty()) {
                $this->addFlash('error', 'En tant que chauffeur, vous devez enregistrer au moins un véhicule.');
                return $this->redirectToRoute('app_user_profile');
            } else {
                $entityManager->flush();
                $this->addFlash('success', 'Vos véhicules ont été mis à jours !');
                return $this->redirectToRoute('app_user_profile');
            }
        }

        // Récupére les véhicules de l'utilisateur
        $userVehicles = $user->getVehicles();

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'profilePictureForm' => $profilePictureForm->createView(),
            'profileForm' => $profileForm->createView(),
            'preferencesForm' => $preferencesForm->createView(),
            'vehicles' => $userVehicles,
            // $addVehiclesForm->createView(), à rajouter pour l'ajout de véhicules.
        ]);
    }
}
