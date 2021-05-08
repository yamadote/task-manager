<?php

namespace App\HeaderLink\Generator;

use App\Entity\User;
use App\HeaderLink\Dto\HeaderLinkDto;
use App\HeaderLink\Repository\HeaderLinkRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HeaderLinkGeneratorTest extends KernelTestCase
{
    private function getHeaderLinkGenerator(): HeaderLinkGenerator
    {
        return new HeaderLinkGenerator(
            new HeaderLinkRepository()
        );
    }

    public function testUserLinksGeneration(): void
    {
        $expected = [
            new HeaderLinkDto("Index", 'app_index'),
            new HeaderLinkDto("Tasks", 'app_task_index'),
            new HeaderLinkDto("Logout", 'app_logout')
        ];
        $actual = $this->getHeaderLinkGenerator()->getLinksByUser(new User());
        self::assertEquals($expected, $actual);
    }

    public function testAnonymousLinksGeneration(): void
    {
        $expected = [
            new HeaderLinkDto("Index", 'app_index'),
            new HeaderLinkDto("Login", 'app_login'),
            new HeaderLinkDto("Register", 'app_register')
        ];
        $actual = $this->getHeaderLinkGenerator()->getLinksByUser(null);
        self::assertEquals($expected, $actual);
    }
}
