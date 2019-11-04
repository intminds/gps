<?php declare(strict_types=1);

namespace Intminds\GPS\Calc;

use Intminds\GPS\Borders\DefaultBordersCalc;
use Intminds\GPS\Point;
use Intminds\GPS\Points;
use Intminds\GPS\Segment;
use Intminds\GPS\Track;
use PHPUnit\Framework\TestCase;

final class DefaultBordersCalcTest extends TestCase
{
    public function testBasic()
    {
        $points1 = new Points();
        $points1[] = new Point(1, 0);
        $points1[] = new Point(2, -0.2);
        $points1[] = new Point(3, 0);

        $points2 = new Points();
        $points2[] = new Point(5, -9);

        $points3 = new Points();
        $points3[] = new Point(7, 1);
        $points3[] = new Point(6, 3);
        $points3[] = new Point(8, 1);

        $points4 = new Points(); // Testing a segment without borders

        $track = new Track();
        $track->appendSegment((new Segment())->setPoints($points1));
        $track->appendSegment((new Segment())->setPoints($points2));
        $track->appendSegment((new Segment())->setPoints($points3));
        $track->appendSegment((new Segment())->setPoints($points4));

        $bc = new DefaultBordersCalc();
        $b = $bc->calcTrackBorders($track);
        $this->assertEqualsWithDelta(1.0, $b->minLat, 0.01);
        $this->assertEqualsWithDelta(8.0, $b->maxLat, 0.01);
        $this->assertEqualsWithDelta(-9.0, $b->minLng, 0.01);
        $this->assertEqualsWithDelta(3.0, $b->maxLng, 0.01);
    }
}