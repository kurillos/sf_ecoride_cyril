<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Employee;
use App\Form\EmployeeType;
use App\Repository\TripRepository;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    private EntityManagerInterface $em;
    private TripRepository $tripRepository;

    public function __construct(EntityManagerInterface $em, TripRepository $tripRepository)
    {
        $this->em = $em;
        $this->tripRepository = $tripRepository;
    }

    #[Route('/', name: 'app_admin_dashboard', methods: ['GET'])]
    public function dahsboard(): Response
    {
        $tripCounts = [];
        $creditsEarnings = [];
        $totalCredits = 0;

        return $this->render('admin/index.html.twig', [
            'tripCounts' => json_encode($tripCounts),
            'creditEarnings' => json_encode($creditsEarnings),
            'totalCredits' => $totalCredits,
        ]);
    }

    #[Route('/create-employee', name: 'app_admin_create_employee')]
    public function createEmployee(Request $request): Response
    {
        $employee =  new Employee();
        $form = $this->createForm(EmployeeType::class, $employee);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($employee);
            $this->em->flush();
            $this->addFlash('success', 'L\'employé a été créé avec succès.');

            return $this->redirectToRoute('app_admin_dashboard');
        }

        return $this->render('admin/create_employee.html.twig', [
            'form' => $form,
        ]);
    }
}