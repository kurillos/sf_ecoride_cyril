<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


class TestController extends AbstractController
{
    #[Route('/test_reset_password/{email}/{newPassword}', name: 'app_test_reset_password')]
    public function resetPassword(
        string $email,
        string $newPassword,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            return new Response('User not found.', Response::HTTP_NOT_FOUND);
        }

        $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        return new Response(sprintf('Password for %s has been reset to: %s', $user->getEmail(), $newPassword));
    }

    #[Route('/test-email', name: 'app_test_email')]
    public function testEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('contact@ecoride.fr')
            ->to('jose@ecoride.fr')
            ->subject('Mail de test')
            ->text('Ceci est un mail de test envoyé depuis Symfony.');

        $mailer->send($email);

        return new Response('Email de test envoyé avec succès. Vérifiez MailHog !');
    }
}