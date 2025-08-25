<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Report;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

use App\Repository\ReportRepository;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_EMPLOYEE')]
class EmployeeController extends AbstractController
{
    #[Route('/employee', name: 'app_employee')]
    public function dashboard(ReportRepository $reportRepository, ReviewRepository $reviewRepository): Response
    {
        $pendingReviews = $reviewRepository->findBy(['status' => 'pending']);
        $pendingReports = $reportRepository->findBy(['status' => 'pending']);

        return $this->render('employee/dashboard.html.twig', [
            'pendingReviews' => $pendingReviews,
            'pendingReports' => $pendingReports,
        ]);
    }

    #[Route('/employee/review/{id}/{action}', name: 'app_employee_review_decision', methods: ['POST'])]
    public function reviewDecision(int $id, string $action, ReviewRepository $reviewRepository): Response
    {
        $review = $reviewRepository->find($id);

        if (!$review) {
            return $this->json(['status' => 'error', 'message' => 'Avis non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        if ($action === 'validate') {
            $review->setStatus('validated');
        } elseif ($action === 'reject') {
            $review->setStatus('rejected');
        }

        $reviewRepository->save($review, true);

        return $this->json(['status' => 'success', 'message' => 'Avis mis à jour.']);
    }

    #[Route('/employee/report/{id}', name: 'app_employee_report_details', methods: ['GET'])]
    public function getReportDetails(int $id, ReportRepository $reportRepository): JsonResponse
    {
        $report = $reportRepository->find($id);

        if (!$report) {
            return $this->json(['message' => 'Signalement non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        $trip = $report->getReportedTrip();
        $driver = $trip->getDriver();
        $passengers = $trip->getPassengers();

        $passengersData = [];
        foreach ($passengers as $passenger) {
            $passengersData[] = [
                'firstName' => $passenger->getFirstName(),
            ];
        }

        $data = [
            'reportedTrip' => [
                'departureLocation' => $trip->getDepartureLocation(),
                'destinationLocation' => $trip->getDestinationLocation(),
                'tripDate' => $trip->getTripDate()->format('Y-m-d'),
                'tripTime' => $trip->getTripDate()->format('H:i:s'),
                'driver' => [
                    'firstName' => $driver->getFirstName(),
                ],
                'passengers' => $passengersData,
            ],
            'reason' => $report->getReason(),
        ];

        return $this->json($data);
    }

    #[Route('/employee/sanction/{id}', name: 'app_employee_sanction', methods: ['POST'])]
    public function sanctionUser(int $id, Request $request, ReportRepository $reportRepository, EntityManagerInterface $em): JsonResponse
    {
        $report = $reportRepository->find($id);

        if (!$report) {
            return $this->json(['message' => 'Signalement non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $sanctionType = $data['type'] ?? 'none';
        $sanctionComment = $data['comment'] ?? '';

        $report->setStatus('processed');
        $em->flush();

        return $this->json(['message' => 'La sanction a été appliquée avec succès.']);
    }
}