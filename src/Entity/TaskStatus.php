<?php

namespace App\Entity;

class TaskStatus
{
    private int $id;
    private string $title;
    private string $slug;
    private string $color;

    public function __construct(int $id, string $title, string $slug, string $color)
    {
        $this->id = $id;
        $this->title = $title;
        $this->slug = $slug;
        $this->color = $color;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getColor(): string
    {
        return $this->color;
    }
}
