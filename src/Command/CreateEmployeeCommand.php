<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-employee',
    description: "Créé un nouvel utilisateur avec le rôle d'employé"
)]

class CreateEmployeeCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, "Email de l'employé")
            ->addArgument('password', InputArgument::REQUIRED, "Mot de passe de l'employé")
            ->addArgument('firstName', InputArgument::REQUIRED, "Prénom de l'employé")
            ->addArgument('lastName', InputArgument::REQUIRED, "Nom de famille de l'employé")
            ->addArgument('pseudo', InputArgument::REQUIRED, "Pseudo de l'employé")
            ->setHelp('Cette commande crée un utilisateur avec le role employé');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $firstName = $input->getArgument('firstName');
        $lastName = $input->getArgument('lastName');
        $pseudo = $input->getArgument('pseudo');

        $user = new User();
        $user->setEmail($email);
        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                $password
            )
        );
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setPseudo($pseudo);

        $user->setRoles(['ROLE_USER', 'ROLE_EMPLOYEE']);

        $this->em->persist($user);
        $this->em->flush();

        $io->success(sprintf("L'utilisateur à été créé avec le role employé.", $email));

        return Command::SUCCESS;
    }
}