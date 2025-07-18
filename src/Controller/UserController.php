<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserPreference;
use App\Entity\Vehicle;
use App\Form\UserProfileFormType;
use App\Form\UserProfilePictureType;
use App\Form\UserPreferenceType;
use App\Form\UserVehiclesCollectionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;



final class UserController extends AbstractController
{
    #[Route('/profile', name: 'app_user_profile')]
    #[IsGranted('ROLE_USER')]
    public function profile(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $userPreference = $user->getUserPreference();

        if (!$userPreference) {
            $userPreference = new UserPreference();
            $user->setUserPreference($userPreference);
        }

        $profileForm = $this->createForm(UserProfileFormType::class, $user);
        $profileForm->handleRequest($request);

        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            $user = $profileForm->getData();

            $currentRoles = $user->getRoles();
            $desiredRole = $user->getDesiredRole();

            if ($desiredRole === 'passenger' && in_array('ROLE_DRIVER', $currentRoles)) {
                $user->setRoles(array_diff($currentRoles, ['ROLE_DRIVER']));
            } elseif (($desiredRole === 'driver' || $desiredRole === 'both') && !in_array('ROLE_DRIVER', $currentRoles)) {
                $user->setRoles(array_unique(array_merge($currentRoles, ['ROLE_DRIVER'])));
            }

            if (!in_array('ROLE_USER', $user->getRoles())) {
                $user->setRoles(array_unique(array_merge($user->getRoles(), ['ROLE_USER'])));
            }

            $entityManager->flush();
            $this->addFlash('success', 'Votre profil a été mis à jour.');
            return $this->redirectToRoute('app_user_profile');
        }


        $profilePictureForm = $this->createForm(UserProfilePictureType::class, $user);
        $profilePictureForm->handleRequest($request);

        if ($profilePictureForm->isSubmitted() && $profilePictureForm->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Votre photo de profil a été mise à jour avec succès.');
            return $this->redirectToRoute('app_user_profile');
        }

        $preferencesForm = $this->createForm(UserPreferenceType::class, $userPreference);
        $preferencesForm->handleRequest($request);

        if ($preferencesForm->isSubmitted() && $preferencesForm->isValid()) {
            $entityManager->flush();
            $entityManager->refresh($user);
            $this->addFlash('success', 'Vos préférences ont été mises à jour !');
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


        $userVehicles = $user->getVehicles();

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'profilePictureForm' => $profilePictureForm->createView(),
            'profileForm' => $profileForm->createView(),
            'preferencesForm' => $preferencesForm->createView(),
            'vehicles' => $userVehicles,
            'vehiclesForm' => $vehiclesForm->createView(),
            // 'addVehiclesForm' => $addVehiclesForm->createView(), // Décommentez si vous l'utilisez
        ]);
    }

    #[Route('/profile/trips-history', name: 'app_driver_trips_history')]
    public function driverTripsHistory(Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = $security->getUser();

        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_login');
        }

        $proposedTrips = $entityManager->getRepository(Trip::class)->findBy(
            ['driver' => $user],
            ['departureTime' => 'ASC']
        );

        // Filtres à venir et passés
        $upcomingTrips = [];
        $pastTrips = [];
        $now = new \DateTimeImmutable();

        forEach ($proposedTrips as $trip) {
            if ($trip->getDepartureTime() > $now) {
                $upcomingTrips[] = $trip;
            } else {
                $pastTrips[] = $trip;
            }
        }

        return $this->render('user/driver_trips_history.html.twig', [
            'user' => $user,
            'upcoming_trips' => $upcomingTrips,
            'past_trips' => $pastTrips,
        ]);
    }
}