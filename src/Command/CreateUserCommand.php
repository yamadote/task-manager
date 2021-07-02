<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateUserCommand extends Command
{
    private const EMAIL_ARGUMENT = 'email';
    private const PASSWORD_ARGUMENT = 'password';

    protected static $defaultName = 'app:create-user';

    private EntityManagerInterface $entityManager;
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function configure(): void
    {
        $this->addArgument(self::EMAIL_ARGUMENT, InputArgument::REQUIRED);
        $this->addArgument(self::PASSWORD_ARGUMENT, InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument(self::EMAIL_ARGUMENT);
        $plainPassword = $input->getArgument(self::PASSWORD_ARGUMENT);
        $user = new User();
        $user->setEmail($email);
        $password = $this->passwordEncoder->encodePassword($user, $plainPassword);
        $user->setPassword($password);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $output->writeln("User '{$user->getEmail()}' was created!");
        return Command::SUCCESS;
    }
}
