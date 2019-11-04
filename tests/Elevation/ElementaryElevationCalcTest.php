<?php
declare(strict_types=1);

namespace Intminds\GPS\Elevation;

use Intminds\GPS\Point;
use Intminds\GPS\Points;
use Intminds\GPS\Segment;
use Intminds\GPS\Track;
use PHPUnit\Framework\TestCase;

final class ElementaryElevationCalcTest extends TestCase
{
    public function testBasic()
    {
        // up 2, down 1
        $points1 = new Points();
        $points1[] = new Point(1, 0, 1.0);
        $points1[] = new Point(2, 0, 2.0);
        $points1[] = new Point(3, 0, 1.0);
        $points1[] = new Point(3, 0, 2.0);

        $points2 = new Points();
        $points2[] = new Point(5, -9, 1.0);

        // up 2, down 0
        $points3 = new Points();
        $points3[] = new Point(6, 1, 7.0);
        $points3[] = new Point(7, 1, 8.0);
        $points3[] = new Point(8, 1, 9.0);

        $points4 = new Points();

        $track = new Track();
        $track->appendSegment((new Segment())->setPoints($points1));
        $track->appendSegment((new Segment())->setPoints($points2));
        $track->appendSegment((new Segment())->setPoints($points3));
        $track->appendSegment((new Segment())->setPoints($points4));

        $c = new ElementaryElevationCalc();
        $this->assertEqualsWithDelta(2, $c->calcPointsElevation($points1)->elevationGain, 0.01);
        $this->assertEqualsWithDelta(1, $c->calcPointsElevation($points1)->elevationLoss, 0.01);
        $this->assertEqualsWithDelta(0, $c->calcPointsElevation($points2)->elevationGain, 0.01);
        $this->assertEqualsWithDelta(0, $c->calcPointsElevation($points2)->elevationLoss, 0.01);
        $this->assertEqualsWithDelta(2, $c->calcPointsElevation($points3)->elevationGain, 0.01);
        $this->assertEqualsWithDelta(0, $c->calcPointsElevation($points3)->elevationLoss, 0.01);
        $this->assertEqualsWithDelta(0, $c->calcPointsElevation($points4)->elevationGain, 0.01);
        $this->assertEqualsWithDelta(0, $c->calcPointsElevation($points4)->elevationLoss, 0.01);
        $this->assertEqualsWithDelta(4, $c->calcTrackElevation($track)->elevationGain, 1.0);
        $this->assertEqualsWithDelta(1, $c->calcTrackElevation($track)->elevationLoss, 1.0);
    }
}