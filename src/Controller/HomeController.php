<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route; // Pour les annotations

class HomeController extends AbstractController
{
     #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/signup', name: 'app_signup')]
    public function signup(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        // Définir les crédits par défaut *avant* de soumettre le formulaire
        $user->setCredits(20);
        $user->setRoles(['ROLE_USER']);

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request); // Traite la soumission du formulaire

        if ($form->isSubmitted() && $form->isValid()) {
            // Encoder le mot de passe
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // Persister l'utilisateur en base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // TODO: Gérer la confirmation d'email si tu actives isVerified (make:registration:form)
            // Par exemple, rediriger vers une page de succès ou la page de connexion
            $this->addFlash('success', 'Votre compte a été créé avec succès !');

            return $this->redirectToRoute('app_home'); // Redirige vers la page d'accueil par exemple
        }

        // Rend le formulaire dans le template Twig
        return $this->render('pages/signup.html.twig', [
            'userForm' => $form->createView(), // Passe la vue du formulaire au template
            'controller_name' => 'HomeController',
        ]);
    }
}