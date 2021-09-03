<?php declare(strict_types = 1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DefaultTest extends KernelTestCase
{
    public function testExecute(): void
    {
        $var = true;
        $this->assertTrue($var);
    }
}
