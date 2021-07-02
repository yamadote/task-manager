<?php

namespace App\Command;

use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MoveTaskCommand extends Command
{
    protected static $defaultName = 'app:move-task';

    private TaskRepository $taskRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(TaskRepository $taskRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->taskRepository = $taskRepository;
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this->addArgument('task', InputArgument::REQUIRED, 'The task that will be moved.');
        $this->addArgument('parent', InputArgument::REQUIRED, 'The task that will be parent.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $task = $this->taskRepository->find((int) $input->getArgument('task'));
        if (null === $task) {
            $output->writeln("Task not found!");
            return Command::FAILURE;
        }
        $parent = $this->taskRepository->find((int) $input->getArgument('parent'));
        if (null === $parent) {
            $output->writeln("Parent not found!");
            return Command::FAILURE;
        }
        if (!$task->getUser()->equals($parent->getUser())) {
            $output->writeln("Task users is not the same!");
            return Command::FAILURE;
        }
        $task->setParent($parent);
        $this->entityManager->flush();
        $output->writeln("Task '{$task->getTitle()}' moved to '{$parent->getTitle()}'!");
        return Command::SUCCESS;
    }
}
