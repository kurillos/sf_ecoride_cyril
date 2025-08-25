<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-user-pseudos',
    description: 'Updates users with null or empty pseudos to have a unique pseudo.',
)]
class UpdateUserPseudosCommand extends Command
{
    private $userRepository;
    private $entityManager;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->setHelp('This command allows you to update users with null or empty pseudos to have a unique pseudo.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $users = $this->userRepository->findBy(['pseudo' => null]);
        $users = array_merge($users, $this->userRepository->findBy(['pseudo' => '']));

        if (empty($users)) {
            $io->success('No users with null or empty pseudos found.');

            return Command::SUCCESS;
        }

        foreach ($users as $user) {
            $user->setPseudo(uniqid('user_'));
            $this->entityManager->persist($user);
        }

        $this->entityManager->flush();

        $io->success(sprintf('Successfully updated %d users.', count($users)));

        return Command::SUCCESS;
    }
}
