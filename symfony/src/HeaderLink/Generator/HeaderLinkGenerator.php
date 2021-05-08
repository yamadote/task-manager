<?php

namespace App\HeaderLink\Generator;

use App\Entity\User;
use App\HeaderLink\Dto\HeaderLinkDto;
use App\HeaderLink\Repository\HeaderLinkRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class HeaderLinkGenerator
{
    /** @var HeaderLinkRepository */
    private $headerLinkRepository;

    public function __construct(HeaderLinkRepository $headerLinkRepository)
    {
        $this->headerLinkRepository = $headerLinkRepository;
    }

    /**
     * @param User|null $user
     * @return HeaderLinkDto[]
     */
    public function getLinksByUser(?UserInterface $user): array
    {
        if (null === $user) {
            return $this->headerLinkRepository->getAnonymousLinks();
        }
        return $this->headerLinkRepository->getUserLinks();
    }
}
