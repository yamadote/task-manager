<?php

namespace App\Tests\Input\Validation\Checker;

use App\Input\Validation\Checker\DateValidationChecker;
use PHPUnit\Framework\TestCase;

class DateValidationCheckerTest extends TestCase
{
    public function testSomething(): void
    {
        $checker = new DateValidationChecker();
        $this->assertTrue($checker->isValid("31/12/2017"));
        $this->assertTrue($checker->isValid("27/6/2022"));

        $this->assertTrue($checker->isValid("29/2/2016"));
        $this->assertTrue($checker->isValid("29/02/2016"));

        $this->assertFalse($checker->isValid("29/2/2017"));
        $this->assertFalse($checker->isValid("29.2/2/2017"));
        $this->assertFalse($checker->isValid("29/2/2017123"));
        $this->assertFalse($checker->isValid("29/2/2s17"));
        $this->assertFalse($checker->isValid("12329/2/2017"));
        $this->assertFalse($checker->isValid("aasdfasdf21321"));
        $this->assertFalse($checker->isValid("29/2/2017-29/2/2018"));
        $this->assertFalse($checker->isValid("29/2/2016/29"));
    }
}
