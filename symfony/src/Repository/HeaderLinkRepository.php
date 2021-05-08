<?php

namespace App\Repository;

use App\Config\HeaderLinkConfig;
use App\Entity\User;
use App\Entity\HeaderLink;
use Symfony\Component\Security\Core\User\UserInterface;

class HeaderLinkRepository
{
    /** @var HeaderLinkConfig */
    private $headerLinkConfig;

    public function __construct(HeaderLinkConfig $headerLinkConfig)
    {
        $this->headerLinkConfig = $headerLinkConfig;
    }

    /**
     * @param User|null $user
     * @return HeaderLink[]
     */
    public function getLinksByUser(?UserInterface $user): array
    {
        if (null === $user) {
            return $this->headerLinkConfig->getAnonymousLinks();
        }
        return $this->headerLinkConfig->getUserLinks();
    }
}
