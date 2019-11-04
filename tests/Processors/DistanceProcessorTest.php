<?php
declare(strict_types=1);

namespace Intminds\GPS\Processors;

use Intminds\GPS\Movement\RoundEarthMovementCalc;
use Intminds\GPS\Point;
use Intminds\GPS\Points;
use Intminds\GPS\Segment;
use Intminds\GPS\Track;
use PHPUnit\Framework\TestCase;

final class DistanceProcessorTest extends TestCase
{
    public function testApplyToTrack()
    {
        $points1 = new Points();
        $points1[] = new Point(1, 0);
        $points1[] = new Point(2, 0);
        $points1[] = new Point(3, 0);

        $points2 = new Points();
        $points2[] = new Point(6, 1);
        $points2[] = new Point(7, 1);
        $points2[] = new Point(8, 1);

        $track = new Track();
        $track->appendSegment((new Segment())->setPoints($points1));
        $track->appendSegment((new Segment())->setPoints($points2));

        $proc = new DistanceProcessor(new RoundEarthMovementCalc());
        $proc->applyToTrack($track);
        $this->assertEqualsWithDelta(0, $points1[0]["distance"] / 1000, 1);
        $this->assertEqualsWithDelta(111, $points1[1]["distance"] / 1000, 1);
        $this->assertEqualsWithDelta(222, $points1[2]["distance"] / 1000, 1);
        $this->assertEqualsWithDelta(222, $points2[0]["distance"] / 1000, 1);
        $this->assertEqualsWithDelta(333, $points2[1]["distance"] / 1000, 1);
        $this->assertEqualsWithDelta(444, $points2[2]["distance"] / 1000, 1);
    }

    public function testOnePointInSegment()
    {
        $points1 = new Points();
        $points1[] = new Point(1, 0);
        $points1[] = new Point(2, 0);
        $points1[] = new Point(3, 0);

        $points2 = new Points();
        $points2[] = new Point(6, 0);

        $points3 = new Points();
        $points3[] = new Point(6, 1);
        $points3[] = new Point(7, 1);
        $points3[] = new Point(8, 1);

        $points4 = new Points();
        $points4[] = new Point(6, 0);

        $track = new Track();
        $track->appendSegment((new Segment())->setPoints($points1));
        $track->appendSegment((new Segment())->setPoints($points2));
        $track->appendSegment((new Segment())->setPoints($points3));
        $track->appendSegment((new Segment())->setPoints($points4));

        $proc = new DistanceProcessor(new RoundEarthMovementCalc());
        $proc->applyToTrack($track);
        $this->assertEqualsWithDelta(0, $points1[0]["distance"] / 1000, 1);
        $this->assertEqualsWithDelta(111, $points1[1]["distance"] / 1000, 1);
        $this->assertEqualsWithDelta(222, $points1[2]["distance"] / 1000, 1);
        $this->assertEqualsWithDelta(222, $points2[0]["distance"] / 1000, 1);
        $this->assertEqualsWithDelta(222, $points3[0]["distance"] / 1000, 1);
        $this->assertEqualsWithDelta(333, $points3[1]["distance"] / 1000, 1);
        $this->assertEqualsWithDelta(444, $points3[2]["distance"] / 1000, 1);
        $this->assertEqualsWithDelta(444, $points4[0]["distance"] / 1000, 1);
    }
}