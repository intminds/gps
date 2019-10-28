<?php
declare(strict_types=1);

namespace Intminds\GPS;

use PHPUnit\Framework\TestCase;

final class PointTest extends TestCase
{
    public function testConstructor()
    {
        $t = time();
        $p = new Point(4.0, 5.0, 6.0, $t);
        $this->assertEqualsWithDelta(4.0, $p->lat, 0.01);
        $this->assertEqualsWithDelta(5.0, $p->lng, 0.01);
        $this->assertEqualsWithDelta(6.0, $p->alt, 0.01);
        $this->assertSame($t, $p->time);

        $p = new Point(2.0, 3.0);
        $this->assertEqualsWithDelta(2.0, $p->lat, 0.01);
        $this->assertNull($p->alt);
        $this->assertNull($p->time);
    }

    public function testArrayAccessBasic()
    {
        $t = time();
        $p = new Point(4.0, 5.0, 6.0, $t);
        $p["distance"] = 1234;
        $this->assertEquals(1234, $p["distance"]);
        $this->assertTrue(isset($p["alt"]));
        $this->assertTrue(isset($p["distance"]));
        $this->assertFalse(isset($p["XXX"]));
        unset($p["XXX"]); // Should not throw
        unset($p["distance"]);
        $this->assertFalse(isset($p["distance"]));
        $this->expectException(\OutOfRangeException::class);
        unset($p["lat"]);
    }

    public function testArrayAccessNonExistingKey()
    {
        $p = new Point(4.0, 5.0, 6.0);
        $p["distance"] = 1234;
        $this->expectException(\OutOfRangeException::class);
        $p["XXX"];
    }

    public function testArrayAccessNullMainProps()
    {
        $p = new Point(1, 2);
        $this->assertNull($p["alt"]);
        $this->assertNull($p["time"]);
    }

    public function testArrayAccessMainProps()
    {
        $p = new Point(1, 2);
        $p["lat"] = 100;
        $p["lng"] = 200;
        $p["alt"] = 300;
        $p["time"] = 400;
        $this->assertEqualsWithDelta(100, $p["lat"], 0.01);
        $this->assertEqualsWithDelta(200, $p["lng"], 0.01);
        $this->assertEqualsWithDelta(300, $p["alt"], 0.01);
        $this->assertEqualsWithDelta(400, $p["time"], 0.01);
    }

    public function testTraversable()
    {
        $p = new Point(4.0, 5.0, 6.0);
        $p["g"] = "G";
        $this->assertSame([
            "lat" => 4.0,
            "lng" => 5.0,
            "alt" => 6.0,
            "time" => null,
            "g" => "G",
        ], iterator_to_array($p));
    }

    public function testClone()
    {
        $p = new Point(1, 1);
        $p["q"] = new \stdClass();
        $p2 = clone $p;
        $this->assertNotSame($p["q"], $p2["q"]);
    }
}