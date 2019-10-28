<?php
declare(strict_types=1);

namespace Intminds\GPS;

use PHPUnit\Framework\TestCase;

final class SegmentTest extends TestCase
{
    public function testBasic()
    {
        $s = new Segment("T");
        $this->assertSame("T", $s->getTitle());
        $s->setTitle("Q");
        $this->assertSame("Q", $s->getTitle());
        $pp = $s->getPoints();
        $pp[] = new Point(1, 1);
        $this->assertSame(1, sizeof($s->getPoints()));
    }

    public function testSetPoints()
    {
        $pp = new Points();
        $pp[] = new Point(1, 1);
        $pp[] = new Point(2, 2);
        $s = new Segment();
        $s->setPoints($pp);
        $this->assertSame(2, sizeof($s->getPoints()));
    }

    public function testTraversable()
    {
        $p1 = new Point(4.0, 5.0, 6.0);
        $p2 = new Point(4.5, 5.5, 6.5);
        $s = new Segment("Title");
        $s->getPoints()->appendPoint($p1);
        $s->getPoints()[] = $p2;
        $this->assertSame([
            "title" => "Title",
            "points" => $s->getPoints(),
        ], iterator_to_array($s));
    }
}
