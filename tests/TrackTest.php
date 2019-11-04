<?php
declare(strict_types=1);

namespace Intminds\GPS;

use PHPUnit\Framework\TestCase;

final class TrackTest extends TestCase
{
    public function testBasic()
    {
        $t = new Track();
        $t->setTitle("TTT");
        $this->assertSame("TTT", $t->getTitle());
        $t->appendSegment(new Segment());
        $this->assertSame(1, sizeof($t->getSegments()));
    }

    public function testStartFinish()
    {
        $t = new Track();
        $this->assertNull($t->getStart());
        $this->assertNull($t->getFinish());
        $s = new Segment();
        $s->appendPoint(new Point(1.0, 2.0));
        $t->appendSegment($s);
        $this->assertEqualsWithDelta(1.0, $t->getStart()->lat, 0.01);
        $this->assertEqualsWithDelta(2.0, $t->getStart()->lng, 0.01);
        $this->assertEqualsWithDelta(1.0, $t->getFinish()->lat, 0.01);
        $this->assertEqualsWithDelta(2.0, $t->getFinish()->lng, 0.01);
        $s->appendPoint(new Point(3.0, 4.0));
        $s->appendPoint(new Point(5.0, 6.0));
        $this->assertEqualsWithDelta(1.0, $t->getStart()->lat, 0.01);
        $this->assertEqualsWithDelta(2.0, $t->getStart()->lng, 0.01);
        $this->assertEqualsWithDelta(5.0, $t->getFinish()->lat, 0.01);
        $this->assertEqualsWithDelta(6.0, $t->getFinish()->lng, 0.01);
        $s2 = new Segment();
        $s2->appendPoint(new Point(19.0, 20.0));
        $t->appendSegment($s2);
        $this->assertEqualsWithDelta(1.0, $t->getStart()->lat, 0.01);
        $this->assertEqualsWithDelta(2.0, $t->getStart()->lng, 0.01);
        $this->assertEqualsWithDelta(19.0, $t->getFinish()->lat, 0.01);
        $this->assertEqualsWithDelta(20.0, $t->getFinish()->lng, 0.01);
    }

    public function testStartFinishWithEmptySegments()
    {
        $t = new Track();
        $s = new Segment();
        $s->appendPoint(new Point(1.0, 2.0));
        $s->appendPoint(new Point(3.0, 4.0));
        $s->appendPoint(new Point(5.0, 6.0));
        $t->appendSegment(new Segment());
        $t->appendSegment($s);
        $t->appendSegment(new Segment());
        $this->assertEqualsWithDelta(1.0, $t->getStart()->lat, 0.01);
        $this->assertEqualsWithDelta(2.0, $t->getStart()->lng, 0.01);
        $this->assertEqualsWithDelta(5.0, $t->getFinish()->lat, 0.01);
        $this->assertEqualsWithDelta(6.0, $t->getFinish()->lng, 0.01);
    }
}
