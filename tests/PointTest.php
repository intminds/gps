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

    public function testArrayAccess()
    {
        $t = time();
        $p = new Point(4.0, 5.0, 6.0, $t);
        $p["distance"] = 1234;
        $this->assertEquals(1234, $p["distance"]);
        $this->assertNull($p["XXX"]);
        $this->assertTrue(isset($p["alt"]));
        $this->assertTrue(isset($p["distance"]));
        $this->assertFalse(isset($p["XXX"]));
        unset($p["XXX"]); // Should not throw
        unset($p["distance"]);
        $this->assertNull($p["distance"]);
        $this->expectException(\OutOfRangeException::class);
        unset($p["lat"]);
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
}
