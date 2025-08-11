<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Trip;
use App\Entity\User;
use App\Entity\Rating;
use App\Form\TripType;
use App\Form\RatingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Psr\Log\LoggerInterface;

final class TripController extends AbstractController
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

            $this->addFlash('success', 'Votre covoiturage a été créé avec succès !');

            return $this->redirectToRoute('app_trip_show', ['id' => $trip->getId()]);
        }

        return $this->render('trip/newtrip.html.twig', [
            'tripForm' => $form->createView(),
        ]);
    }

    #[Route('/trip/{id}', name: 'app_trip_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Trip $trip, Security $security, EntityManagerInterface $entityManager): Response
    {
        // Déterminer si l'utilisateur actuel est le conducteur
        $isDriver = $this->getUser() === $trip->getDriver();
        
        $user = $security->getUser();
        $isBooked = false;
        $hasEnoughCredits = false;

        if ($user instanceof \App\Entity\User) {
            $booking = $entityManager->getRepository(Booking::class)->findOneBy([
                'trip' => $trip,
                'user' => $user,
                'status' => 'confirmed'
            ]);
            $isBooked = ($booking !== null);
            $hasEnoughCredits = $user->getCredits() >= $trip->getPricePerSeat();
        }

        $hasRemainingSeats = $trip->getRemainingSeats() > 0;

        return $this->render('trip/show.html.twig', [
            'trip' => $trip,
            'isDriver' => $isDriver,
            'isBooked' => $isBooked,
            'hasRemainingSeats' => $hasRemainingSeats,
            'hasEnoughCredits' => $hasEnoughCredits,
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
    public function book(Request $request, Trip $trip, EntityManagerInterface $entityManager, Security $security, MailerInterface $mailer, LoggerInterface $logger): Response
    {
        $user = $security->getUser();
        $seats = $request->request->getInt('seats', 1);
        
        if (!$user instanceof \App\Entity\User) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour réserver un covoiturage.');
        }

        $logger->info('Début de la tentative de réservation pour le trajet ' . $trip->getId());

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

                try {
                    $logger->info('La réservation a été confirmée, tentative d\'envoi d\'e-mails.');

                    // Envoi confirmation par mail au passager
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
                    $logger->info('E-mail de confirmation au passager envoyé avec succès.');

                    // Envoi de confirmation par mail au conducteur
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
                    $logger->info('E-mail de notification au conducteur envoyé avec succès.');

                } catch (\Exception $e) {
                    $logger->error('Échec de l\'envoi de l\'e-mail : ' . $e->getMessage());
                    $this->addFlash('error', 'Erreur lors de l\'envoi des e-mails de confirmation. Veuillez contacter le support.');
                }
                
                $this->addFlash('success', 'Votre réservation a été confirmée et vos crédits ont été débités !');
            } else {
                $logger->warning('Pas assez de crédits pour la réservation.');
                $this->addFlash('error', 'Vous n\'avez pas assez de crédits pour réserver ce trajet.');
            }
        } else {
            $logger->warning('Pas assez de places disponibles pour la réservation.');
            $this->addFlash('error', 'Il n\'y a pas assez de places disponibles.');
        }

        $logger->info('Fin de la tentative de réservation.');
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
    public function cancel(Request $request, Trip $trip, EntityManagerInterface $entityManager, MailerInterface $mailer, LoggerInterface $logger): Response
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

                try {
                    // Envoyer un e-mail d'annulation au passager
                    $email = (new Email())
                        ->from('no-reply@ecoride.com')
                        ->to($passenger->getEmail())
                        ->subject('Annulation du covoiturage EcoRide')
                        ->html($this->renderView('emails/cancellation_notification.html.twig', [
                            'trip' => $trip,
                            'passenger' => $passenger,
                        ]));
                    $mailer->send($email);
                } catch (\Exception $e) {
                    $logger->error('Failed to send cancellation email: ' . $e->getMessage());
                    $this->addFlash('error', 'Erreur lors de l\'envoi de l\'e-mail d\'annulation. Veuillez contacter le support.');
                }
            }
            $booking->setStatus('cancelled_by_driver');
        }

        $trip->setStatus('cancelled');
        $entityManager->flush();

        $this->addFlash('success', 'Le trajet a été annulé. Les passagers ont été remboursés et notifiés.');

        return $this->redirectToRoute('app_user_trips_history');
    }

    #[Route('/trip/start/{id}', name: 'app_trip_start')]
    public function start(Trip $trip, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() !== $trip->getDriver()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à démarrer ce trajet.');
        }

        if ($trip->getStatus() !== 'scheduled') {
            $this->addFlash('error', 'Ce trajet ne peut pas être démarré car il n\'est pas dans l\'état prévu.');
            return $this->redirectToRoute('app_trip_show', ['id' => $trip->getId()]);
        } else {
            // Mettre à jour le statut du trajet
            $trip->setStatus('in_progress');
            $entityManager->flush();

            $this->addFlash('success', 'Le trajet a été démarré avec succès.');
        }

        return $this->redirectToRoute('app_trip_show', ['id' => $trip->getId()]);
    }

    #[Route('/trip/complete/{id}', name: 'app_trip_complete')]
    public function complete(Trip $trip, EntityManagerInterface $entityManager, MailerInterface $mailer, LoggerInterface $logger): Response
    {
        $logger->info('Début de la méthode complete pour le trajet ' . $trip->getId());

        if ($this->getUser() !== $trip->getDriver()) {
            $logger->error('Accès refusé: l\'utilisateur n\'est pas le conducteur du trajet.');
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à marquer ce trajet comme terminé.');
        }

        if ($trip->getStatus() !== 'in_progress') {
            $logger->warning('Tentative de terminer un trajet qui n\'est pas en cours. Statut actuel: ' . $trip->getStatus());
            $this->addFlash('error', 'Ce trajet ne peut pas être marqué comme terminé car il n\'est pas en cours.');
        } else {
            $logger->info('Le trajet est en cours. Mise à jour du statut en "completed".');
            // Mettre à jour le statut du trajet
            $trip->setStatus('completed');
            $entityManager->flush();

            $logger->info('Le statut du trajet a été mis à jour. Envoi des e-mails aux passagers.');
            // Notifier les passagers
            foreach ($trip->getBookings() as $booking) {
                $participant = $booking->getUser();

                try {
                    $logger->info('Préparation de l\'e-mail pour le participant ' . $participant->getEmail());
                    // Création du mail de notification
                    $email = (new Email())
                        ->from('no-reply@ecoride.com')
                        ->to($participant->getEmail())
                        ->subject('Trajet EcoRide terminé : validez le trajet !')
                        ->html($this->renderView('emails/trip_completed.html.twig', [
                            'trip' => $trip,
                            'participant' => $participant,
                        ]));
                    $mailer->send($email);
                    $logger->info('E-mail envoyé avec succès au participant ' . $participant->getEmail());
                } catch (\Exception $e) {
                    $logger->error('Échec de l\'envoi de l\'e-mail au participant ' . $participant->getEmail() . ': ' . $e->getMessage());
                    $this->addFlash('error', 'Erreur lors de l\'envoi des e-mails de validation. Veuillez contacter le support.');
                }
            }

            $this->addFlash('success', 'Le covoiturage est terminé. Les passagers ont été notifiés par email pour valider le trajet.');
        }
        $logger->info('Fin de la méthode complete.');
        return $this->redirectToRoute('app_trip_show', ['id' => $trip->getId()]);
    }

    #[Route('/trip/validate/{trip}/{userToRate}', name: 'app_trip_validate_good', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function validateGoodAction(Request $request, Trip $trip, User $userToRate, EntityManagerInterface $em): Response
    {

        $currentUser = $this->getUser();
           if (!$trip->getPassengers()->contains($currentUser)) {
            // Empêche un utilisateur de noter un trajet auquel il n'a pas participé
            throw $this->createAccessDeniedException("Vous ne pouvez pas noter ce trajet car vous n'y avez pas participé.");
        }

        if ($userToRate->getId() !== $trip->getDriver()->getID()) {
            throw $this->createNotFoundException('L\'utilisateur spécifié n\'est pas le conducteur de ce trajet.');
        }

        $rating = new Rating();
        $rating->setRatingUser($currentUser);
        $rating->setRatedUser($userToRate);
        $rating->setTrip($trip);

        $form = $this->createForm(RatingType::class, $rating);
        $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                dd($form->getErrors(true));
            }
        
           
            $ratingValue = $request->request->get('rating_value');
            $rating->setRating((int)$ratingValue);
            
            $em->persist($rating);
            $em->flush();

            $this->addFlash('success', 'Merci pour votre évaluation du conducteur !');
            return $this->redirectToRoute('app_home');
        }

            return $this->render('trip/validate_rating.html.twig', [
                'trip' => $trip,
                'userToRate' => $userToRate,
                'form' => $form->createView(),
            ]);
    }

    #[Route('/trip/report/{id}/{user_id}', name:'app_trip_report')]
    public function report(Trip $trip, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à signaler ce trajet.');
        }

        $booking = $entityManager->getRepository(Booking::class)->findOneBy([
            'trip' => $trip,
            'user' => $user,
        ]);

        if (!$booking) {
            throw $this->createNotFoundException('Réservation non trouvée pour ce trajet et cet utilisateur.');
        }

        $booking->setStatus('reported');
        $entityManager->flush();

        $this->addFlash('success', 'Votre signalement a été pris en compte. Un employé va traiter votre signalement dans les plus brefs délais.');
        return $this->redirectToRoute('app_home');
    }
}