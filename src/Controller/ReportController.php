<?php

namespace App\Controller;

use App\Entity\Report;
use App\Entity\Trip;
use App\Repository\ReportRepository;
use App\Form\ReportType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ReportController extends AbstractController
{
    private ReportRepository $reportRepository;

    public function __construct(ReportRepository $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    #[Route('/trip/{id}/report', name: 'app_report_trip', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Trip $trip, Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser();

        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecter pour signaler un covoiturage.');
            return $this->redirectToRoute('app_login');
        }

        if ($user === $trip->getDriver()) {
            $this->addFlash('error', 'Vous ne pouvez pas signaler votre propre trajet.');
            return $this->redirectToRoute('app_trip_show', ['id' => $trip->getId()]);
        }

        $isPassenger = false;
        foreach ($trip->getPassengers() as $passenger) {
            if ($passenger->getUser() === $user) {
                $isPassenger = true;
                break;
            }
        }
        if (!$isPassenger) {
            $this->addFlash('error', 'Vous ne pouvez signaler que les covoiturages auxquels vous avez participé.');
            return $this->redirectToRoute('app_trip_search');
        }
        
        $report = new Report();
        $report->setReporter($user);
        $report->setReportedTrip($trip);

        $form = $this->createForm(ReportType::class, $report);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($report);
            $em->flush();

            $this->addFlash('success', 'Votre signalement a été envoyé avec succès. Un employé traitera votre signalement dans les plus brefs délais.');

            return $this->redirectToRoute('app_trip_show', ['id' => $trip->getId()]);
        }

        return $this->render('report/new.html.twig', [
            'form' => $form->createView(),
            'trip' => $trip,
        ]);
    }
    
    #[Route('/employee/report/{id}', name: 'app_employee_report_details', methods: ['GET'])]
    public function getReportDetails(Report $report): JsonResponse
    {   
        if (!$report) {
            return $this->json(['message' => 'Le signalement n\'existe pas.'], Response::HTTP_NOT_FOUND);
        }

        try {
            $reportedTrip = $report->getReportedTrip();
            $driver = $reportedTrip->getDriver();
            $passengers = $reportedTrip->getPassengers();

            return $this->json([
            'id' => $report->getId(),
            'reason' => $report->getReason(),
            'reportedTrip' => [
                'id' => $reportedTrip->getId(),
                'departureLocation' => $reportedTrip->getDepartureLocation(),
                'destinationLocation' => $reportedTrip->getDestinationLocation(),
                'tripDate' => $reportedTrip->getDepartureTime()->format('Y-m-d'),
                'tripTime' => $reportedTrip->getDepartureTime()->format('H:i'),
                'driver' => [
                    'id' => $driver->getId(),
                    'firstName' => $driver->getFirstName(),
                ],
                'passengers' => array_map(function($passenger) {
                    return [
                        'id' => $passenger->getId(),
                        'firstName' => $passenger->getFirstName(),
                    ];
                }, $passengers->toArray())
            ]
        ]);
    } catch (\Exception $e) {
        return $this->json(['message' => 'Erreur lors de la récupération des détails du signalement.', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
}
