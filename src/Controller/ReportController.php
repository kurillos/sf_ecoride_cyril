<?php

namespace App\Controller;

use App\Entity\Report;
use App\Entity\Trip;
use App\Form\ReportType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
Use Symfony\Component\Security\Http\Attribute\IsGranted;

class ReportController extends AbstractController
{
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
            'reportForm' => $form->createView(),
            'trip' => $trip,
        ]);
    }
}