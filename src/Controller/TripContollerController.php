<?php

namespace App\Controller;

use App\Entity\Trip;
use App\Form\TripType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class TripContollerController extends AbstractController
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
}
