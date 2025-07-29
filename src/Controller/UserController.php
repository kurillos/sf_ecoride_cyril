<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\User;
use App\Entity\UserPreference;
use App\Entity\Vehicle;
use App\Entity\Trip;
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
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


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
        ]);
    }

    #[Route('/profile/trips-history', name: 'app_user_trips_history')]
    #[IsGranted('ROLE_USER')]
    public function tripsHistory(Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = $security->getUser();

        $driverTrips = $entityManager->getRepository(Trip::class)->findBy(['driver' => $user]);
        $passengerBookings = $entityManager->getRepository(Booking::class)->findBy(['user' => $user]);

        return $this->render('user/trips_history.html.twig', [
            'driverTrips' => $driverTrips,
            'passengerBookings' => $passengerBookings,
        ]);
    }

    #[Route('/profile/credits', name: 'app_user_credits')]
    #[IsGranted('ROLE_USER')]
    public function credits(Security $security): Response
    {
        $user = $security->getUser();

        return $this->render('user/credits.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/booking/{id}/cancel', name: 'app_booking_cancel', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function cancelBooking(Request $request, Booking $booking, EntityManagerInterface $entityManager, Security $security, MailerInterface $mailer): Response
    {
        $user = $security->getUser();

        // Valider le jeton CSRF
        if (!$this->isCsrfTokenValid('cancel' . $booking->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Jeton de sécurité invalide. Veuillez réessayer.');
            return $this->redirectToRoute('app_user_trips_history');
        }

        if ($booking->getUser() !== $user) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à annuler cette réservation.');
            return $this->redirectToRoute('app_user_trips_history');
        }

        if ($booking->getTrip()->getDepartureTime() < new \DateTimeImmutable()) {
            $this->addFlash('error', 'Vous ne pouvez pas annuler une réservation pour un trajet déjà passé.');
            return $this->redirectToRoute('app_user_trips_history');
        }

        try {
            $refundAmount = $booking->getSeats() * $booking->getTrip()->getPricePerSeat();
            $user->setCredits($user->getCredits() + $refundAmount);

            $booking->setStatus('cancelled');

            $trip = $booking->getTrip();
            $trip->setAvailableSeats($trip->getAvailableSeats() + $booking->getSeats());

            $entityManager->flush();

            // Send cancellation email to passenger
            $emailPassenger = (new Email())
                ->from('no-reply@ecoride.com')
                ->to($user->getEmail())
                ->subject('Annulation de votre réservation EcoRide')
                ->html($this->renderView('emails/cancellation_confirmation.html.twig', [
                    'booking' => $booking,
                    'trip' => $trip,
                    'user' => $user,
                ]));

            $mailer->send($emailPassenger);

            // Send cancellation email to driver
            $emailDriver = (new Email())
                ->from('no-reply@ecoride.com')
                ->to($trip->getDriver()->getEmail())
                ->subject('Annulation d\'une réservation pour votre covoiturage EcoRide')
                ->html($this->renderView('emails/cancellation_driver_notification.html.twig', [
                    'booking' => $booking,
                    'trip' => $trip,
                    'driver' => $trip->getDriver(),
                    'passenger' => $user,
                ]));

            $mailer->send($emailDriver);

            $this->addFlash('success', 'Votre réservation a été annulée et vos crédits ont été recrédités.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de l\'annulation de la réservation : ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_user_trips_history');
    }
}