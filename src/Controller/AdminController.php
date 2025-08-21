<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TripRepository;
use App\Repository\BookingRepository;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    private EntityManagerInterface $em;
    private TripRepository $tripRepository;
    private BookingRepository $bookingRepository;
    private UserRepository $userRepository;

    public function __construct(EntityManagerInterface $em, TripRepository $tripRepository, BookingRepository $bookingRepository, UserRepository $userRepository)
    {
        $this->em = $em;
        $this->tripRepository = $tripRepository;
        $this->bookingRepository = $bookingRepository;
        $this->userRepository = $userRepository;
    }

    #[Route('/', name: 'app_admin_dashboard', methods: ['GET'])]
    public function dashboard(): Response
    {
        $tripCounts = $this->tripRepository->countByDate();
        $creditEarnings = $this->bookingRepository->getEarningsByDate();
        $totalCredits = $this->bookingRepository->getTotalEarnings();
        $users = $this->userRepository->findAll();

        return $this->render('admin/dashboard.html.twig', [
            'tripCounts' => json_encode($tripCounts),
            'creditEarnings' => json_encode($creditEarnings),
            'totalCredits' => $totalCredits,
            'users' => $users,
        ]);
    }

    #[Route('/user/{id}/promote', name: 'app_admin_user_promote', methods: ['POST'])]
    public function promoteUser(User $user): Response
    {
        $roles = $user->getRoles();
        $roles[] = 'ROLE_EMPLOYEE';
        $user->setRoles(array_unique($roles));

        $this->em->flush();

        $this->addFlash('success', sprintf('L\'utilisateur %s a été promu employé.', $user->getEmail()));

        return $this->redirectToRoute('app_admin_dashboard');
    }

    #[Route('/user/{id}/demote', name: 'app_admin_user_demote', methods: ['POST'])]
    public function demoteUser(User $user): Response
    {
        $roles = $user->getRoles();
        $newRoles = array_filter($roles, function($role) {
            return $role !== 'ROLE_EMPLOYEE';
        });
        $user->setRoles($newRoles);

        $this->em->flush();

        $this->addFlash('success', sprintf('Le rôle employé a été retiré à l\'utilisateur %s.', $user->getEmail()));

        return $this->redirectToRoute('app_admin_dashboard');
    }

    #[Route('/suspend-account', name: 'app_admin_suspend_account', methods: ['POST'])]
    public function suspendAccount(Request $request): Response
    {
        $this->addFlash('success', 'Le compte a été suspendu.');
        return $this->redirectToRoute('app_admin_dashboard');
    }
}
