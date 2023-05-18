<?php

namespace App\Tests\Controller;

use App\Controller\TaskController;
use PHPUnit\Framework\TestCase;

class TaskControllerTest extends TestCase
{
    public function test(){
        $this->assertTrue(true);
        $this->assertEquals("a", "a");
        $this->assertEquals("a", "a");
        $this->assertTrue(true);
        $this->assertFalse(false);
        $this->assertEquals("a", "a");
    }
}
