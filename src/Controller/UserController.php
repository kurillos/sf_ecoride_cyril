<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserProfileFormType;
use App\Form\UserProfilePictureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Form\UserProfilePreferencesType;

final class UserController extends AbstractController
{
    #[Route('/profile', name: 'app_user_profile')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profile(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();//Récupére l'utilisateur connecté

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Crée le formulaire pour la photo de profil
        $profilePictureForm = $this->createForm(UserProfilePictureType::class, $user);
        $profilePictureForm->handleRequest($request);

        if ($profilePictureForm->isSubmitted() && $profilePictureForm->isValid()) {
            // VichUpload gère automatiquement l'upload de la photo de profil
            $entityManager->persist($user);
            $entityManager->flush();

            $user->setProfilePictureFile(null);

            $this->addFlash('success', 'Votre photo de profil a été mise à jour avec succès.');

            return $this->redirectToRoute('app_user_profile');
        }
        

        $preferencesForm = $this->createForm(UserProfilePreferencesType::class, $user);
        $preferencesForm->handleRequest($request);

        if ($preferencesForm->isSubmitted() && $preferencesForm->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Vos préférences ont été mises à jour !');
            return $this->redirectToRoute('app_user_profile');
        }

        $profileForm = $this->createForm(UserProfileFormType::class, $user);
        $profileForm->handleRequest($request);

        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            $desiredRoleChoice = $profileForm->get('desiredRole')->getData();

            $roles = ['ROLE_USER']; // Role par défaut

            $isDriver = false;
            if ($desiredRoleChoice === 'driver' || $desiredRoleChoice === 'both') {
                $roles[] = 'ROLE_DRIVER';
                $isDriver = true;
            }

            $roles = array_unique($roles);

            $user->setRoles($roles);
            $user->setIsDriver($isDriver);

            $entityManager->flush();
            $this->addFlash('success', 'Vos informations personnelles ont été mises à jour !');
            return $this->redirectToRoute('app_user_profile');
        }
        
        $currentRoles = $user->getRoles();
        $initialDesireRole = 'passenger';
        if (in_array('ROLE_DRIVER', $currentRoles) && in_array('ROLE_USER', $currentRoles) && count($currentRoles) > 1) {
            $initialDesireRole = 'both';
        } elseif (in_array('ROLE_DRIVER', $currentRoles)) {
            $initialDesireRole = 'driver';
        }
        
        $profileForm->get('desiredRole')->setData($initialDesireRole);

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'profilePictureForm' => $profilePictureForm->createView(),
            'profileForm' => $profileForm->createView(),
            'preferencesForm' => $preferencesForm->createView(),
        ]);

        

    }
}
