<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @Gedmo\Tree(type="nested")
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $link = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $reminder = null;

    /**
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="smallint")
     */
    private int $status;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     */
    private User $user;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private DateTimeInterface $updatedAt;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    private int $lft;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    private int $lvl;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    private int $rgt;

    /**
     * @Gedmo\TreeRoot
     * @ORM\ManyToOne(targetEntity="Task")
     * @ORM\JoinColumn(name="tree_root", referencedColumnName="id", onDelete="CASCADE")
     */
    private Task $root;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Task", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private ?Task $parent;

    /**
     * @ORM\OneToMany(targetEntity="Task", mappedBy="parent", cascade={"remove"})
     * @ORM\OrderBy({"lft" = "ASC"})
     * @var Collection|Task[]
     */
    private $children;

    /**
     * @ORM\OneToMany(targetEntity=UserTaskSettings::class, mappedBy="task", orphanRemoval=true)
     * @var Collection|UserTaskSettings[]
     */
    private Collection $usersSettings;

    /**
     * @ORM\OneToMany(targetEntity=TrackedPeriod::class, mappedBy="task", orphanRemoval=true)
     * @var Collection|TrackedPeriod[]
     */
    private Collection $trackedPeriods;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description = null;

    /**
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    private int $trackedTime = 0;

    /**
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    private int $childrenTrackedTime = 0;

    public function __construct()
    {
        $this->usersSettings = new ArrayCollection();
        $this->trackedPeriods = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): void
    {
        $this->link = $link;
    }

    public function getReminder(): ?DateTimeInterface
    {
        return $this->reminder;
    }

    public function setReminder(?DateTimeInterface $reminder): void
    {
        $this->reminder = $reminder;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function isReminder(): bool
    {
        if(empty($this->reminder)) {
            return false;
        }
        return $this->reminder->getTimestamp() < (new DateTime())->getTimestamp();
    }

    public function getLft(): int
    {
        return $this->lft;
    }

    public function setLft(int $lft): void
    {
        $this->lft = $lft;
    }

    public function getLvl(): int
    {
        return $this->lvl;
    }

    public function setLvl(int $lvl): void
    {
        $this->lvl = $lvl;
    }

    public function getRgt(): int
    {
        return $this->rgt;
    }

    public function setRgt(int $rgt): void
    {
        $this->rgt = $rgt;
    }

    public function getRoot(): Task
    {
        return $this->root;
    }

    public function setRoot(Task $root): void
    {
        $this->root = $root;
    }

    public function getParent(): ?Task
    {
        return $this->parent;
    }

    public function setParent(?Task $parent): void
    {
        $this->parent = $parent;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function hasChildren(): bool
    {
        return !$this->getChildren()->isEmpty();
    }

    public function equals(Task $task): bool
    {
        return $this->id === $task->getId();
    }

    /**
     * @return Collection|UserTaskSettings[]
     */
    public function getUsersSettings(): Collection
    {
        return $this->usersSettings;
    }

    /**
     * @return Collection|TrackedPeriod[]
     */
    public function getTrackedPeriods(): Collection
    {
        return $this->trackedPeriods;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getTrackedTime(): int
    {
        return $this->trackedTime;
    }

    public function setTrackedTime(int $trackedTime): void
    {
        $this->trackedTime = $trackedTime;
    }

    public function increaseTrackedTime(int $increase): void
    {
        $this->trackedTime += $increase;
    }

    public function getChildrenTrackedTime(): int
    {
        return $this->childrenTrackedTime;
    }

    public function setChildrenTrackedTime(int $childrenTrackedTime): void
    {
        $this->childrenTrackedTime = $childrenTrackedTime;
    }

    public function increaseChildrenTrackedTime(int $increase): void
    {
        $this->childrenTrackedTime += $increase;
    }
}
