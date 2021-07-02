<?php

namespace App\Repository;

use App\Builder\UserTaskSettingsBuilder;
use App\Entity\Task;
use App\Entity\User;
use App\Entity\UserTaskSettings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserTaskSettings|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserTaskSettings|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserTaskSettings[]    findAll()
 * @method UserTaskSettings[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserTaskSettingsRepository extends ServiceEntityRepository
{
    private UserTaskSettingsBuilder $settingsBuilder;

    public function __construct(ManagerRegistry $registry, UserTaskSettingsBuilder $settingsBuilder)
    {
        parent::__construct($registry, UserTaskSettings::class);
        $this->settingsBuilder = $settingsBuilder;
    }

    /**
     * @param Task[] $tasks
     * @return UserTaskSettings[]
     */
    public function findByTasks(array $tasks): array
    {
        $raw = $this->findBy(['task' => $tasks]);
        $settings = [];
        foreach ($raw as $setting) {
            $settings[$setting->getTask()->getId()] = $setting;
        }
        return $settings;
    }

    public function findByUserAndTask(User $user, Task $task): UserTaskSettings
    {
        $setting = $this->findOneBy(['user' => $user, 'task' => $task]);
        return $setting ?? $this->settingsBuilder->buildDefaultSettings($user, $task);
    }
}
