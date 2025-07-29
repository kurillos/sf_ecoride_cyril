<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Trip;
use App\Form\TripType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class TripContoller extends AbstractController
{
    #[Route('/trip/new', name: 'app_trip_new')]
    #[IsGranted('ROLE_DRIVER', message: 'Vous devez être un conducteur pour créer un covoiturage.')]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $user = $security->getUser();
        if (!$user instanceof \App\Entity\User) {
            throw $this->createAccessDeniedException('Vous devez être connecté en tant que conducteur pour accéder à cette page.');
        }

        $trip = new Trip();
        $trip->setDriver($user);

        $form = $this->createForm(TripType::class, $trip, [
            'current_user' => $user,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($trip);
            $entityManager->flush();

            $this->addFlash('success', 'Votre covoiturage a été créer avec succès !');

            return $this->redirectToRoute('app_trip_show', ['id' => $trip->getId()]);
        }

        return $this->render('trip/newtrip.html.twig', [
            'tripForm' => $form->createView(),
        ]);
    }

    #[Route('/trip/{id}', name: 'app_trip_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Trip $trip): Response
    {
        return $this->render('trip/show.html.twig', [
            'trip' => $trip,
        ]);
    }

    #[Route('/trip/search', name: 'app_trip_search', methods: ['GET'])]
    public function search(Request $request, EntityManagerInterface $entityManager): Response
    {
        $departure = $request->query->get('departure');
        $destination = $request->query->get('destination');
        $date = $request->query->get('date');

        $trips = $entityManager->getRepository(Trip::class)->findBySearchCriteria($departure, $destination, $date);

        return $this->render('trip/search_results.html.twig', [
            'trips' => $trips,
            'departure' => $departure,
            'destination' => $destination,
            'date' => $date,
        ]);
    }

    #[Route('/trip/{id}/book', name: 'app_trip_book', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function book(Request $request, Trip $trip, EntityManagerInterface $entityManager, Security $security, MailerInterface $mailer): Response
    {
        $user = $security->getUser();
        $seats = $request->request->getInt('seats', 1);

        if ($trip->getRemainingSeats() >= $seats) {
            $cost = $trip->getPricePerSeat() * $seats;

            if ($user->getCredits() >= $cost) {
                $booking = new Booking();
                $booking->setUser($user);
                $booking->setTrip($trip);
                $booking->setSeats($seats);
                $booking->setBookedAt(new \DateTimeImmutable());
                $booking->setStatus('confirmed');

                $user->setCredits($user->getCredits() - $cost);

                $entityManager->persist($booking);
                $entityManager->persist($user);
                $entityManager->flush();

                // Send confirmation email to passenger
                $emailPassenger = (new Email())
                    ->from('no-reply@ecoride.com')
                    ->to($user->getEmail())
                    ->subject('Confirmation de votre réservation EcoRide')
                    ->html($this->renderView('emails/booking_confirmation.html.twig', [
                        'booking' => $booking,
                        'trip' => $trip,
                        'user' => $user,
                    ]));

                $mailer->send($emailPassenger);

                // Send confirmation email to driver
                $emailDriver = (new Email())
                    ->from('no-reply@ecoride.com')
                    ->to($trip->getDriver()->getEmail())
                    ->subject('Nouvelle réservation pour votre covoiturage EcoRide')
                    ->html($this->renderView('emails/booking_driver_notification.html.twig', [
                        'booking' => $booking,
                        'trip' => $trip,
                        'driver' => $trip->getDriver(),
                        'passenger' => $user,
                    ]));

                $mailer->send($emailDriver);

                $this->addFlash('success', 'Votre réservation a été confirmée et vos crédits ont été débités !');
            } else {
                $this->addFlash('error', 'Vous n\'avez pas assez de crédits pour réserver ce trajet.');
            }
        } else {
            $this->addFlash('error', 'Il n\'y a pas assez de places disponibles.');
        }

        return $this->redirectToRoute('app_trip_show', ['id' => $trip->getId()]);
    }

    #[Route('/trip/{id}/edit', name: 'app_trip_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_DRIVER', message: 'Vous devez être le conducteur de ce covoiturage pour le modifier.')]
    public function edit(Request $request, Trip $trip, EntityManagerInterface $entityManager, Security $security): Response
    {
        $user = $security->getUser();

        if ($trip->getDriver() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier ce covoiturage.');
        }

        $form = $this->createForm(TripType::class, $trip, [
            'current_user' => $user,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le covoiturage a été mis à jour avec succès !');

            return $this->redirectToRoute('app_trip_show', ['id' => $trip->getId()]);
        }

        return $this->render('trip/edit.html.twig', [
            'trip' => $trip,
            'tripForm' => $form->createView(),
        ]);
    }

    #[Route('/trip/{id}/cancel', name: 'app_trip_cancel', methods: ['POST'])]
    #[IsGranted('ROLE_DRIVER')]
    public function cancel(Request $request, Trip $trip, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        if ($trip->getDriver() !== $this->getUser()) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à annuler ce trajet.');
            return $this->redirectToRoute('app_user_trips_history');
        }

        if ($trip->getStatus() === 'cancelled') {
            $this->addFlash('error', 'Ce trajet est déjà annulé.');
            return $this->redirectToRoute('app_user_trips_history');
        }

        if ($trip->getDepartureTime() < new \DateTimeImmutable()) {
            $this->addFlash('error', 'Vous ne pouvez pas annuler un trajet déjà passé.');
            return $this->redirectToRoute('app_user_trips_history');
        }

        // Rembourser les passagers
        foreach ($trip->getBookings() as $booking) {
            if ($booking->getStatus() === 'confirmed') {
                $passenger = $booking->getUser();
                $refundAmount = $booking->getSeats() * $trip->getPricePerSeat();
                $passenger->setCredits($passenger->getCredits() + $refundAmount);

                // Envoyer un e-mail d\'annulation au passager
                $email = (new Email())
                    ->from('no-reply@ecoride.com')
                    ->to($passenger->getEmail())
                    ->subject('Annulation du covoiturage EcoRide')
                    ->html($this->renderView('emails/cancellation_notification.html.twig', [
                        'trip' => $trip,
                        'passenger' => $passenger,
                    ]));
                $mailer->send($email);
            }
            $booking->setStatus('cancelled_by_driver');
        }

        $trip->setStatus('cancelled');
        $entityManager->flush();

        $this->addFlash('success', 'Le trajet a été annulé. Les passagers ont été remboursés et notifiés.');

        return $this->redirectToRoute('app_user_trips_history');
    }
}