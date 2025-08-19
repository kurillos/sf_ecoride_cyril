<?php

namespace App\Controller;

use App\Entity\Review;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ReviewController extends AbstractController
{
    #[Route('/employee/review/{action}/{id}', name: 'app_employee_review_decision', methods: ['POST'])]
    #[IsGranted('ROLE_EMPLOYEE')]
    public function reviewDecision(string $action, Review $review = null, EntityManagerInterface $em): JsonResponse
    {
        try {
            if (!$review) {
                return $this->json(['message' => 'L\'avis n\'existe pas.'], Response::HTTP_NOT_FOUND);
            }

            if ($action !== 'validate' && $action !== 'reject') {
                return $this->json(['message' => 'Action invalide.'], Response::HTTP_BAD_REQUEST);
            }

            if ($action === 'validate') {
                $review->setStatus('validated');
                $review->setValidatedAt(new \DateTimeImmutable());
            } elseif ($action === 'reject') {
                $review->setStatus('rejected');
                $review->setRejectedAt(new \DateTimeImmutable());
            }

            $em->flush();

            return $this->json(['message' => "L'avis a été " . ($action === 'validate' ? 'validé' : 'refusé') . ' avec succès.'], Response::HTTP_OK);

        } catch (\Throwable $e) {
            return $this->json(['message' => 'Une erreur est survenue lors du traitement de l\'avis.', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
