<?php

use App\Entity\Rating;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Dotenv\Dotenv;

require __DIR__.'/vendor/autoload.php';

(new Dotenv())->bootEnv(__DIR__.'/.env');

$kernel = new App\Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();

/** @var EntityManagerInterface $entityManager */
$entityManager = $kernel->getContainer()->get('doctrine')->getManager();

// Find two existing users
$user1 = $entityManager->getRepository(User::class)->find(1); // User giving the rating
$user2 = $entityManager->getRepository(User::class)->find(2); // User receiving the rating

if (!$user1 || !$user2) {
    echo 'Error: Could not find two users with IDs 1 and 2. Please ensure they exist.\n';
    exit(1);
}

$rating = new Rating();
$rating->setRatingUser($user1);
$rating->setRatedUser($user2);
$rating->setRating(5); // Example rating
$rating->setComment('Excellent chauffeur, trajet très agréable et ponctuel !');
$rating->setCreatedAt(new DateTimeImmutable());

$entityManager->persist($rating);
$entityManager->flush();

echo 'Test rating created successfully!\n';

$kernel->shutdown();
