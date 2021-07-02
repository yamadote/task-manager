<?php

namespace App\Entity;

use App\Repository\UserTaskSettingsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserTaskSettingsRepository::class)
 */
class UserTaskSettings
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private User $user;

    /**
     * @ORM\ManyToOne(targetEntity=Task::class, inversedBy="usersSettings")
     * @ORM\JoinColumn(nullable=false)
     */
    private Task $task;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $isAdditionalPanelOpen;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $isChildrenOpen;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getTask(): Task
    {
        return $this->task;
    }

    public function setTask(Task $task): void
    {
        $this->task = $task;
    }

    public function getIsAdditionalPanelOpen(): ?bool
    {
        return $this->isAdditionalPanelOpen;
    }

    public function setIsAdditionalPanelOpen(bool $isAdditionalPanelOpen): void
    {
        $this->isAdditionalPanelOpen = $isAdditionalPanelOpen;
    }

    public function getIsChildrenOpen(): ?bool
    {
        return $this->isChildrenOpen;
    }

    public function setIsChildrenOpen(bool $isChildrenOpen): void
    {
        $this->isChildrenOpen = $isChildrenOpen;
    }
}
