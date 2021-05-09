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

    /** @var array */
    private $routeParams;

    /** @var HeaderLink[] */
    private $subLinks;

    public function __construct(int $id, string $title, string $route, array $routeParams = [], array $subLinks = [])
    {
        $this->id = $id;
        $this->title = $title;
        $this->route = $route;
        $this->routeParams = $routeParams;
        $this->subLinks = $subLinks;
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

    public function getRouteParams(): array
    {
        return $this->routeParams;
    }

    public function isDropdown(): bool
    {
        return !empty($this->subLinks);
    }

    /**
     * @return HeaderLink[]
     */
    public function getSubLinks(): array
    {
        return $this->subLinks;
    }

    public function isActive(string $currentRoute): bool
    {
        if ($this->route === $currentRoute) {
            return true;
        }
        foreach ($this->subLinks as $subLink) {
            if ($subLink->isActive($currentRoute)) {
                return true;
            }
        }
        return false;
    }

    public function getParentRoute(): string
    {
        return $this->getRoute() . "_parent";
    }

    public function getParentRouteParams(Task $parent): array
    {
        $params = $this->getRouteParams();
        $params['parent'] = $parent->getId();
        return $params;
    }
}
