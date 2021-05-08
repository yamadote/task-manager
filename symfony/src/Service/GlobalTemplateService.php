<?php

namespace App\Service;

use App\HeaderLink\Dto\HeaderLinkDto;
use App\HeaderLink\Generator\HeaderLinkGenerator;
use Symfony\Component\Security\Core\Security;

class GlobalTemplateService
{
    /** @var Security */
    private $security;

    /** @var HeaderLinkGenerator */
    private $headerLinkGenerator;

    public function __construct(Security $security, HeaderLinkGenerator $headerLinkGenerator)
    {
        $this->security = $security;
        $this->headerLinkGenerator = $headerLinkGenerator;
    }

    /**
     * @return HeaderLinkDto[]
     */
    public function generateHeaderLinks(): array
    {
        $user = $this->security->getUser();
        return $this->headerLinkGenerator->getLinksByUser($user);
    }
}
