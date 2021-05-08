<?php

namespace App\HeaderLink\Dto;

class HeaderLinkDto
{
    /** @var string */
    private $title;

    /** @var string */
    private $route;

    public function __construct(string $title, string $route)
    {
        $this->title = $title;
        $this->route = $route;
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
