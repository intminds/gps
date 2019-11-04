<?php
declare(strict_types=1);

namespace Intminds\GPS;

use Intminds\GPS\Processors\DistanceProcessor;
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

    public function testGetBorders()
    {
        $t = new Track();
        $s = new Segment();
        $s->appendPoint(new Point(1.0, 2.0));
        $s->appendPoint(new Point(3.0, 4.0));
        $s->appendPoint(new Point(5.0, 6.0));
        $t->appendSegment($s);
        $b = $t->calcBorders();
        $this->assertSame(1.0, $b->minLat);
        $this->assertSame(5.0, $b->maxLat);
        $this->assertSame(2.0, $b->minLng);
        $this->assertSame(6.0, $b->maxLng);
    }

    public function testGetDistance()
    {
        $t = new Track();
        $s = new Segment();
        $s->appendPoint(new Point(1.0, 0.0));
        $s->appendPoint(new Point(2.0, 0.0));
        $s->appendPoint(new Point(3.0, 0.0));
        $t->appendSegment($s);
        $this->assertEqualsWithDelta(222, $t->calcDistance() / 1000, 1.0);
    }

    public function testGetElevation()
    {
        $t = new Track();
        $s = new Segment();
        $s->appendPoint(new Point(1.0, 0.0, 1.0));
        $s->appendPoint(new Point(2.0, 0.0, 2.0));
        $s->appendPoint(new Point(3.0, 0.0, 3.0));
        $t->appendSegment($s);
        $this->assertEqualsWithDelta(2, $t->calcElevation()->elevationGain, 0.01);
    }

    public function testApplyProcessor()
    {
        $t = new Track();
        $s = new Segment();
        $s->appendPoint(new Point(1.0, 0.0, 1.0));
        $s->appendPoint(new Point(2.0, 0.0, 2.0));
        $s->appendPoint(new Point(3.0, 0.0, 3.0));
        $t->appendSegment($s);
        $t->applyProcessor(new DistanceProcessor());
        $this->assertEqualsWithDelta(0, $s->getPoints()[0]["distance"], 0.01);
        $this->assertEqualsWithDelta(111, $s->getPoints()[1]["distance"] / 1000, 1.0);
        $this->assertEqualsWithDelta(222, $s->getPoints()[2]["distance"] / 1000, 1.0);
    }
}
