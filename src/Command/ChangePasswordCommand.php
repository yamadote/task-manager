<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ChangePasswordCommand extends Command
{
    private const USER_ID_ARGUMENT = 'userId';
    private const PASSWORD_ARGUMENT = 'password';

    protected static $defaultName = 'app:change-password';

    /** @var UserRepository */
    private $userRepository;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /**
     * ChangePasswordCommand constructor.
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function configure(): void
    {
        $this->addArgument(self::USER_ID_ARGUMENT, InputArgument::REQUIRED);
        $this->addArgument(self::PASSWORD_ARGUMENT, InputArgument::REQUIRED);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $userId = (int) $input->getArgument(self::USER_ID_ARGUMENT);
        $plainPassword = (int) $input->getArgument(self::PASSWORD_ARGUMENT);

        $user = $this->userRepository->find($userId);
        if (null === $user) {
            $output->writeln("User not found!");
            return Command::FAILURE;
        }
        $password = $this->passwordEncoder->encodePassword($user, $plainPassword);
        $user->setPassword($password);
        $this->entityManager->flush();
        $output->writeln("User '{$user->getEmail()}' password was changed!");
        return Command::SUCCESS;
    }
}
