<?php
declare(strict_types=1);

namespace Intminds\GPS\Elevation;

use Intminds\GPS\Point;
use Intminds\GPS\Points;
use Intminds\GPS\Segment;
use Intminds\GPS\Track;
use PHPUnit\Framework\TestCase;

final class MedianHysteresisElevationCalcTest extends TestCase
{
    public function testBasic()
    {
        // up 6-7, down 0-1
        $points1 = new Points();
        $points1[] = new Point(1, 0, 1.0);
        $points1[] = new Point(2, 0, 2.0);
        $points1[] = new Point(3, 0, 1.0);
        $points1[] = new Point(4, 0, 2.0);
        $points1[] = new Point(5, 0, 8.0);
        $points1[] = new Point(6, 0, 7.0);

        $points2 = new Points();
        $points2[] = new Point(5, -9, 1.0);

        $points3 = new Points();

        // up 0-1, down 99-100
        $points4 = new Points();
        $points4[] = new Point(5, 1, 100.0);
        $points4[] = new Point(6, 1, 99.0);
        $points4[] = new Point(7, 1, 50.0);
        $points4[] = new Point(8, 1, 0.0);
        $points4[] = new Point(9, 1, 1.0);

        $track = new Track();
        $track->appendSegment((new Segment())->setPoints($points1));
        $track->appendSegment((new Segment())->setPoints($points2));
        $track->appendSegment((new Segment())->setPoints($points3));
        $track->appendSegment((new Segment())->setPoints($points4));

        $c = new MedianHysteresisElevationCalc(4.0);

        $e = $c->calcPointsElevation($points1);
        $this->assertGreaterThanOrEqual(6.0, $e->elevationGain);
        $this->assertLessThanOrEqual(7.0, $e->elevationGain);
        $this->assertGreaterThanOrEqual(0.0, $e->elevationLoss);
        $this->assertLessThanOrEqual(1.0, $e->elevationLoss);

        $this->assertEqualsWithDelta(0, $c->calcPointsElevation($points2)->elevationGain, 0.01);
        $this->assertEqualsWithDelta(0, $c->calcPointsElevation($points2)->elevationLoss, 0.01);

        $this->assertEqualsWithDelta(0, $c->calcPointsElevation($points3)->elevationGain, 0.01);
        $this->assertEqualsWithDelta(0, $c->calcPointsElevation($points3)->elevationLoss, 0.01);

        $e = $c->calcPointsElevation($points4);
        $this->assertGreaterThanOrEqual(0.0, $e->elevationGain);
        $this->assertLessThanOrEqual(1.0, $e->elevationGain);
        $this->assertGreaterThanOrEqual(99.0, $e->elevationLoss);
        $this->assertLessThanOrEqual(100.0, $e->elevationLoss);
    }
}