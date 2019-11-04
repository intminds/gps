<?php
declare(strict_types=1);

namespace Intminds\GPS\Distance;

use Intminds\GPS\Point;
use Intminds\GPS\Points;
use Intminds\GPS\Processors\DistanceProcessor;
use Intminds\GPS\Processors\MissingPropException;
use Intminds\GPS\Segment;
use Intminds\GPS\Track;
use PHPUnit\Framework\TestCase;

final class PropDistanceCalcTest extends TestCase
{
    public function testBasic()
    {
        $points1 = new Points();
        $points1[] = new Point(1, 0);
        $points1[] = new Point(2, 0);
        $points1[] = new Point(3, 0);

        $points2 = new Points();
        $points2[] = new Point(5, -9);

        $points3 = new Points();
        $points3[] = new Point(6, 1);
        $points3[] = new Point(7, 1);
        $points3[] = new Point(8, 1);

        $points4 = new Points();

        $track = new Track();
        $track->appendSegment((new Segment())->setPoints($points1));
        $track->appendSegment((new Segment())->setPoints($points2));
        $track->appendSegment((new Segment())->setPoints($points3));
        $track->appendSegment((new Segment())->setPoints($points4));

        $proc = new DistanceProcessor();
        $proc->applyToTrack($track);

        $c = new PropDistanceCalc();
        $this->assertEqualsWithDelta(222 /* km */, $c->calcPointsDistance($points1) / 1000, 1.0);
        $this->assertEqualsWithDelta(0, $c->calcPointsDistance($points2) / 1000, 1.0);
        $this->assertEqualsWithDelta(222 /* km */, $c->calcPointsDistance($points3) / 1000, 1.0);
        $this->assertEqualsWithDelta(0, $c->calcPointsDistance($points4) / 1000, 1.0);
        $this->assertEqualsWithDelta(444 /* km */, $c->calcTrackDistance($track) / 1000, 1.0);
    }

    public function testPointsException()
    {
        $points1 = new Points();
        $points1[] = new Point(1, 0);
        $c = new PropDistanceCalc();
        $this->expectException(MissingPropException::class);
        $c->calcPointsDistance($points1);
    }

    public function testTrackException()
    {
        $points1 = new Points();
        $points1[] = new Point(1, 0);
        $track = new Track();
        $track->appendSegment((new Segment())->setPoints($points1));
        $c = new PropDistanceCalc();
        $this->expectException(MissingPropException::class);
        $c->calcTrackDistance($track);
    }
}
