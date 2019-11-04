<?php
declare(strict_types=1);

namespace Intminds\GPS;

use PHPUnit\Framework\TestCase;

final class PointsTest extends TestCase
{
    public function testBasic()
    {
        $p1 = new Point(1, 2);
        $p2 = new Point(3, 4);
        $pp = new Points();
        $this->assertNull($pp->getStart());
        $this->assertNull($pp->getFinish());
        $pp->appendPoint($p1);
        $pp->appendPoint($p2);
        $this->assertSame($p1, $pp->getStart());
        $this->assertSame($p2, $pp->getFinish());
    }

    public function testArrayAccessAndCountable()
    {
        $p1 = new Point(1, 2);
        $p2 = new Point(3, 4);
        $pp = new Points();
        $pp->appendPoint($p1);
        $this->assertSame(1, sizeof($pp));
        $pp[] = $p2;
        $this->assertTrue(isset($pp[1]));
        $this->assertSame(2, sizeof($pp));
        unset($pp[1]);
        $this->assertFalse(isset($pp[1]));
        $this->assertSame(1, sizeof($pp));
        $this->assertNull($pp[999]);
    }

    public function testArrayAccessNonExistingKeyUnset()
    {
        $p1 = new Point(1, 2);
        $pp = new Points();
        $pp->appendPoint($p1);
        $this->expectException(\OutOfRangeException::class);
        unset($pp[1]);
    }

    public function testArrayAccessNonExistingKeySet()
    {
        $p1 = new Point(1, 2);
        $pp = new Points();
        $pp->appendPoint($p1);
        $pp[1] = $p1; // Should not throw
        unset($pp[1]);
        $this->expectException(\OutOfRangeException::class);
        $pp[2] = $p1;
    }

    public function testArrayAccessUnexpectedValueException()
    {
        $pp = new Points();
        $this->expectException(\UnexpectedValueException::class);
        $pp[] = new \stdClass();
    }

    public function testTraversable()
    {
        $p1 = new Point(4.0, 5.0, 6.0);
        $p2 = new Point(4.5, 5.5, 6.5);
        $pp = new Points();
        $pp[] = $p1;
        $pp[] = $p2;
        $this->assertSame([$p1, $p2], iterator_to_array($pp));
    }

    public function testReplaceWith()
    {
        $p1 = new Point(1, 1);
        $p2 = new Point(2, 2);
        $p3 = new Point(3, 3);
        $p4 = new Point(4, 4);
        $p5 = new Point(5, 5);
        $pp = new Points();
        $pp->appendPoint($p1);
        $pp->appendPoint($p2);
        $pp->appendPoint($p3);
        $pp2 = new Points();
        $pp2->appendPoint($p4);
        $pp2->appendPoint($p5);
        $pp->replaceWith($pp2);
        $this->assertSame(2, sizeof($pp));
        $this->assertSame($p4, $pp->getStart());
        $this->assertSame($p5, $pp->getFinish());
    }

    public function testStartFinish1()
    {
        $p1 = new Point(1, 1);
        $p2 = new Point(2, 2);
        $p3 = new Point(3, 3);
        $pp = new Points();
        $pp->appendPoint($p1);
        $this->assertSame($p1, $pp->getStart());
        $this->assertSame($p1, $pp->getFinish());
        $pp->appendPoint($p2);
        $pp->appendPoint($p3);
        $this->assertSame($p1, $pp->getStart());
        $this->assertSame($p3, $pp->getFinish());
    }

    public function testStartFinish2()
    {
        $p1 = new Point(1, 1);
        $p2 = new Point(2, 2);
        $p3 = new Point(3, 3);
        $pp = new Points();
        $pp[] = $p1;
        $this->assertSame($p1, $pp->getStart());
        $this->assertSame($p1, $pp->getFinish());
        $pp[] = $p2;
        $pp[] = $p3;
        $this->assertSame($p1, $pp->getStart());
        $this->assertSame($p3, $pp->getFinish());
    }

    public function testStartFinish3()
    {
        $p1 = new Point(1, 1);
        $p2 = new Point(2, 2);
        $p3 = new Point(3, 3);
        $p4 = new Point(4, 4);
        $p5 = new Point(5, 5);
        $pp = new Points();
        $pp[0] = $p1;
        $this->assertSame($p1, $pp->getStart());
        $this->assertSame($p1, $pp->getFinish());
        $pp[0] = $p2;
        $this->assertSame($p2, $pp->getStart());
        $this->assertSame($p2, $pp->getFinish());
        $pp[1] = $p3;
        $this->assertSame($p2, $pp->getStart());
        $this->assertSame($p3, $pp->getFinish());
        $pp[1] = $p4;
        $this->assertSame($p2, $pp->getStart());
        $this->assertSame($p4, $pp->getFinish());
        $pp[0] = $p5;
        $this->assertSame($p5, $pp->getStart());
        $this->assertSame($p4, $pp->getFinish());
    }

    public function testAllPointsHaveProp()
    {
        $p1 = new Point(1, 2);
        $p1["distance"] = 1000;
        $p1["speed"] = 10.4;
        $p2 = new Point(3, 4, 100.0);
        $p2["distance"] = 1002;
        $pp = new Points();
        $pp[] = $p1;
        $pp[] = $p2;
        $this->assertTrue($pp->allPointsHaveProp("lat"));
        $this->assertFalse($pp->allPointsHaveProp("alt"));
        $this->assertFalse($pp->allPointsHaveProp("time"));
        $this->assertTrue($pp->allPointsHaveProp("distance"));
        $this->assertFalse($pp->allPointsHaveProp("speed"));
        $this->assertFalse($pp->allPointsHaveProp("xxx"));
    }

    public function testClone()
    {
        $p1 = new Point(1, 2);
        $pp = new Points();
        $pp->appendPoint($p1);
        $pp2 = clone $pp;
        $this->assertNotSame($p1, $pp2[0]);
    }
}