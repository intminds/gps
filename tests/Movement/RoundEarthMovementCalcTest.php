<?php
declare(strict_types=1);

namespace Intminds\GPS\Movement;

use Intminds\GPS\Point;
use PHPUnit\Framework\TestCase;

final class RoundEarthMovementCalcTest extends TestCase
{
    public function testGetDistance()
    {
        $point1 = new Point(1, 0);
        $point2 = new Point(2, 0);
        $c = new RoundEarthMovementCalc();
        $this->assertEqualsWithDelta(111 /* km */, $c->getDistance($point1, $point2) / 1000, 1.0);
    }
}
