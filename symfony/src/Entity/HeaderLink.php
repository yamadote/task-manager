<?php

namespace App\Entity;

class HeaderLink
{
    /** @var int */
    private $id;

    /** @var string */
    private $title;

    /** @var string */
    private $route;

    public function __construct(int $id, string $title, string $route)
    {
        $this->id = $id;
        $this->title = $title;
        $this->route = $route;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getRoute(): string
    {
        return $this->route;
    }
}
