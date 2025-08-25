<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Trip;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
     #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $latestTrips = $entityManager->getRepository(Trip::class)->findBy(
            [],
            ['createdAt' => 'DESC'],
            4
        );
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'latest_trips' => $latestTrips,
        ]);
    }

    #[Route('/signup', name: 'app_signup')]
    public function signup(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $user->setCredits(20);
        $user->setRoles(['ROLE_USER']);

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte a été créé avec succès !');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('pages/signup.html.twig', [
            'userForm' => $form->createView(),
            'controller_name' => 'HomeController',
        ]);
    }
}