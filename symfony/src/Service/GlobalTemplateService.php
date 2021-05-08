<?php

namespace App\Service;

use App\Entity\HeaderLink;
use App\Repository\HeaderLinkRepository;
use Symfony\Component\Security\Core\Security;

class GlobalTemplateService
{
    /** @var Security */
    private $security;

    /** @var HeaderLinkRepository */
    private $headerLinkGenerator;

    public function __construct(Security $security, HeaderLinkRepository $headerLinkGenerator)
    {
        $this->security = $security;
        $this->headerLinkGenerator = $headerLinkGenerator;
    }

    /**
     * @return HeaderLink[]
     */
    public function generateHeaderLinks(): array
    {
        $user = $this->security->getUser();
        return $this->headerLinkGenerator->getLinksByUser($user);
    }
}
