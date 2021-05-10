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
            new HeaderLink(4, "Tasks", 'app_task_index', [], [
                new HeaderLink(9, "In Progress", 'app_task_status', ['status' => 'progress'], [], false),
                new HeaderLink(8, "Frozen", 'app_task_status', ['status' => 'frozen']),
                new HeaderLink(10, "Potential", 'app_task_status', ['status' => 'potential']),
                new HeaderLink(11, "Cancelled", 'app_task_status', ['status' => 'cancelled']),
                new HeaderLink(13, "Completed", 'app_task_status', ['status' => 'completed']),
                new HeaderLink(6, "All", 'app_task_index', []),
            ]),
            new HeaderLink(7, "Todo", 'app_task_todo'),
            new HeaderLink(5, "Reminders", 'app_task_reminders', [], [], false),
            new HeaderLink(3, "Logout", 'app_logout')
        ];
        $actual = $this->getHeaderLinkGenerator()->getLinksByUser(new User());
        self::assertEquals($expected, $actual);
    }

    public function testAnonymousLinksGeneration(): void
    {
        $expected = [
            new HeaderLink(1, "Login", 'app_login'),
            new HeaderLink(2, "Register", 'app_register')
        ];
        $actual = $this->getHeaderLinkGenerator()->getLinksByUser(null);
        self::assertEquals($expected, $actual);
    }
}
