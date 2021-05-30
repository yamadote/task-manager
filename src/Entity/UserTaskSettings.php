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
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Task::class, inversedBy="usersSettings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $task;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAdditionalPanelOpen;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isChildrenOpen;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTask(): Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): self
    {
        $this->task = $task;

        return $this;
    }

    public function getIsAdditionalPanelOpen(): ?bool
    {
        return $this->isAdditionalPanelOpen;
    }

    public function setIsAdditionalPanelOpen(bool $isAdditionalPanelOpen): self
    {
        $this->isAdditionalPanelOpen = $isAdditionalPanelOpen;

        return $this;
    }

    public function getIsChildrenOpen(): ?bool
    {
        return $this->isChildrenOpen;
    }

    public function setIsChildrenOpen(bool $isChildrenOpen): self
    {
        $this->isChildrenOpen = $isChildrenOpen;

        return $this;
    }
}
