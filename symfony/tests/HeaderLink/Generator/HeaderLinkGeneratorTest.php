<?php

namespace App\Repository;

use App\Config\HeaderLinkConfig;
use App\Entity\User;
use App\Entity\HeaderLink;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HeaderLinkGeneratorTest extends KernelTestCase
{
    private function getHeaderLinkGenerator(): HeaderLinkRepository
    {
        return new HeaderLinkRepository(
            new HeaderLinkConfig()
        );
    }

    public function testUserLinksGeneration(): void
    {
        $expected = [
            new HeaderLink(0, "Index", 'app_index'),
            new HeaderLink(4, "Tasks", 'app_task_index'),
            new HeaderLink(3, "Logout", 'app_logout')
        ];
        $actual = $this->getHeaderLinkGenerator()->getLinksByUser(new User());
        self::assertEquals($expected, $actual);
    }

    public function testAnonymousLinksGeneration(): void
    {
        $expected = [
            new HeaderLink(0, "Index", 'app_index'),
            new HeaderLink(1, "Login", 'app_login'),
            new HeaderLink(2, "Register", 'app_register')
        ];
        $actual = $this->getHeaderLinkGenerator()->getLinksByUser(null);
        self::assertEquals($expected, $actual);
    }
}
