<?php

namespace App\Entity;

class TaskStatus
{
    /** @var int */
    private $id;

    /** @var string */
    private $title;

    /** @var string */
    private $slug;

    /** @var string */
    private $color;

    /**
     * TaskStatus constructor.
     * @param int $id
     * @param string $title
     * @param string $slug
     * @param string $color
     */
    public function __construct(int $id, string $title, string $slug, string $color)
    {
        $this->id = $id;
        $this->title = $title;
        $this->slug = $slug;
        $this->color = $color;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }
}
