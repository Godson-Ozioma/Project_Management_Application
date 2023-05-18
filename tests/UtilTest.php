<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

class UtilTest extends TestCase
{
    public function testSomething(): void
    {
        $this->assertTrue(true);
        $this->assertEquals("a", "a");
        $this->assertEquals("a", "a");
        $this->assertTrue(true);
        $this->assertFalse(false);
        $this->assertEquals("a", "a");
    }
}
