<?php

namespace App\Controller;

use App\Entity\Trip;
use App\Form\TripType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TripContollerController extends AbstractController
{
    #[Route('/trip/new', name: 'app_trip_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $user = $security->getUser();
        if (!$user || !in_array('ROLE_DRIVER', $user->getRoles())) {
            $this->addFlash('error', 'Vous devez être connecté et avoir le rôle chauffeur pour créer un voyage.');
            return $this->redirectToRoute('app_login');
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

            $this->addFlash('success', 'Votre voyage a été créer avec succès !');

            return $this->redirectToRoute('app_trip_show', ['id' => $trip->getId()]);
        }

        return $this->render('trip/newtrip.html.twig', [
            'tripForm' => $form,
        ]);
    }
}
