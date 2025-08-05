<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class MailerController extends AbstractController
{
    #[Route('/email', name: 'app_send_email')]
    public function sendEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('contact@ecoride.fr')
            ->to('jose@ecoride.fr')
            ->subject('Test Email EcoRide')
            ->text('Ceci est un test d\'envoi d\'email avec Symfony Mailer.')
            ->html('<p>Ceci est un test d\'envoi d\'email avec <strong>Symfony Mailer</strong>.</p>');

        $mailer->send($email);

        return new Response('Email envoyé ! Vérifie Mailtrap.');
    }
}