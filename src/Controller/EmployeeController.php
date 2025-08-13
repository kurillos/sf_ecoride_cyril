<?php

namespace App\Controller;

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
}