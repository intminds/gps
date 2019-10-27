<?php
declare(strict_types=1);

namespace Intminds\GPS;

use PHPUnit\Framework\TestCase;

final class PointTest extends TestCase
{
    public function testBasic()
    {
        $p = new Point(2.0, 3.0);
        $this->assertSame($p->lat, 2.0);
    }
}
