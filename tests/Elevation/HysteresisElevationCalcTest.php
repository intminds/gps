<?php
declare(strict_types=1);

namespace Intminds\GPS\Elevation;

use Intminds\GPS\Point;
use Intminds\GPS\Points;
use Intminds\GPS\Segment;
use Intminds\GPS\Track;
use PHPUnit\Framework\TestCase;

final class HysteresisElevationCalcTest extends TestCase
{
    public function testBasic()
    {
        // up 7, down 1
        $points1 = new Points();
        $points1[] = new Point(1, 0, 1.0);
        $points1[] = new Point(2, 0, 2.0);
        $points1[] = new Point(3, 0, 1.0);
        $points1[] = new Point(4, 0, 2.0);
        $points1[] = new Point(5, 0, 8.0);
        $points1[] = new Point(6, 0, 7.0);

        $points2 = new Points();
        $points2[] = new Point(5, -9, 1.0);

        // up 0, down 100
        $points3 = new Points();
        $points3[] = new Point(6, 1, 100.0);
        $points3[] = new Point(7, 1, 50.0);
        $points3[] = new Point(8, 1, 1.0);
        $points3[] = new Point(9, 1, 0.0);

        $points4 = new Points();

        // up 1, down 100
        $points5 = new Points();
        $points5[] = new Point(5, 1, 100.0);
        $points5[] = new Point(6, 1, 99.0);
        $points5[] = new Point(7, 1, 50.0);
        $points5[] = new Point(8, 1, 0.0);
        $points5[] = new Point(9, 1, 1.0);

        // up 100, down 1
        $points6 = new Points();
        $points6[] = new Point(6, 1, 0.0);
        $points6[] = new Point(7, 1, 50.0);
        $points6[] = new Point(8, 1, 100.0);
        $points6[] = new Point(9, 1, 99.0);

        $track = new Track();
        $track->appendSegment((new Segment())->setPoints($points1));
        $track->appendSegment((new Segment())->setPoints($points2));
        $track->appendSegment((new Segment())->setPoints($points3));
        $track->appendSegment((new Segment())->setPoints($points4));
        $track->appendSegment((new Segment())->setPoints($points5));
        $track->appendSegment((new Segment())->setPoints($points6));

        $c = new HysteresisElevationCalc(4.0);
        $this->assertEqualsWithDelta(7, $c->calcPointsElevation($points1)->elevationGain, 0.01);
        $this->assertEqualsWithDelta(1, $c->calcPointsElevation($points1)->elevationLoss, 0.01);
        $this->assertEqualsWithDelta(0, $c->calcPointsElevation($points2)->elevationGain, 0.01);
        $this->assertEqualsWithDelta(0, $c->calcPointsElevation($points2)->elevationLoss, 0.01);
        $this->assertEqualsWithDelta(0, $c->calcPointsElevation($points3)->elevationGain, 0.01);
        $this->assertEqualsWithDelta(100, $c->calcPointsElevation($points3)->elevationLoss, 0.01);
        $this->assertEqualsWithDelta(0, $c->calcPointsElevation($points4)->elevationGain, 0.01);
        $this->assertEqualsWithDelta(0, $c->calcPointsElevation($points4)->elevationLoss, 0.01);
        $this->assertEqualsWithDelta(1, $c->calcPointsElevation($points5)->elevationGain, 0.01);
        $this->assertEqualsWithDelta(100, $c->calcPointsElevation($points5)->elevationLoss, 0.01);
        $this->assertEqualsWithDelta(100, $c->calcPointsElevation($points6)->elevationGain, 0.01);
        $this->assertEqualsWithDelta(1, $c->calcPointsElevation($points6)->elevationLoss, 0.01);
        $this->assertEqualsWithDelta(108, $c->calcTrackElevation($track)->elevationGain, 0.01);
        $this->assertEqualsWithDelta(202, $c->calcTrackElevation($track)->elevationLoss, 0.01);
        $this->assertEqualsWithDelta(6, $c->calcPointsElevationWithOffset($points1, 5)->elevationGain, 0.01);
        $this->assertEqualsWithDelta(0, $c->calcPointsElevationWithOffset($points1, 5)->elevationLoss, 0.01);
        $this->assertEqualsWithDelta(0, $c->calcPointsElevationWithOffset($points5, 4)->elevationGain, 0.01);
        $this->assertEqualsWithDelta(99, $c->calcPointsElevationWithOffset($points5, 4)->elevationLoss, 0.01);
        $this->assertEqualsWithDelta(99, $c->calcPointsElevationWithOffset($points6, 3)->elevationGain, 0.01);
        $this->assertEqualsWithDelta(0, $c->calcPointsElevationWithOffset($points6, 3)->elevationLoss, 0.01);
    }

    public function testInvalidOffset()
    {
        $points1 = new Points();
        $points1[] = new Point(1, 0, 1.0);
        $points1[] = new Point(2, 0, 2.0);

        $c = new HysteresisElevationCalc(4.0);
        $this->expectException(\OutOfBoundsException::class);
        $c->calcPointsElevationWithOffset($points1, 2);
    }

    public function testWithZeroMinimalChange()
    {
        // up 8, down 2
        $points1 = new Points();
        $points1[] = new Point(1, 0, 1.0);
        $points1[] = new Point(2, 0, 2.0);
        $points1[] = new Point(3, 0, 1.0);
        $points1[] = new Point(3, 0, 2.0);
        $points1[] = new Point(3, 0, 8.0);
        $points1[] = new Point(3, 0, 7.0);

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

        $c = new HysteresisElevationCalc(0.0); // With 0.0 it should be identical to ElementaryElevationCalc
        $this->assertEqualsWithDelta(8, $c->calcPointsElevation($points1)->elevationGain, 0.01);
        $this->assertEqualsWithDelta(2, $c->calcPointsElevation($points1)->elevationLoss, 0.01);
        $this->assertEqualsWithDelta(0, $c->calcPointsElevation($points2)->elevationGain, 0.01);
        $this->assertEqualsWithDelta(0, $c->calcPointsElevation($points2)->elevationLoss, 0.01);
        $this->assertEqualsWithDelta(2, $c->calcPointsElevation($points3)->elevationGain, 0.01);
        $this->assertEqualsWithDelta(0, $c->calcPointsElevation($points3)->elevationLoss, 0.01);
        $this->assertEqualsWithDelta(0, $c->calcPointsElevation($points4)->elevationGain, 0.01);
        $this->assertEqualsWithDelta(0, $c->calcPointsElevation($points4)->elevationLoss, 0.01);
        $this->assertEqualsWithDelta(10, $c->calcTrackElevation($track)->elevationGain, 0.01);
        $this->assertEqualsWithDelta(2, $c->calcTrackElevation($track)->elevationLoss, 0.01);
    }
}