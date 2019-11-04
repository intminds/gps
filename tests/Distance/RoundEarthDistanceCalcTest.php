<?php
declare(strict_types=1);

namespace Intminds\GPS\Calc;

use Intminds\GPS\Distance\RoundEarthDistanceCalc;
use Intminds\GPS\Point;
use Intminds\GPS\Points;
use Intminds\GPS\Segment;
use Intminds\GPS\Track;
use PHPUnit\Framework\TestCase;

final class RoundEarthDistanceCalcTest extends TestCase
{
    /**
     * @var Points
     */
    private $points;
    /**
     * @var Points
     */
    private $points2;

    public function setUp()
    {
        $this->points = new Points();
        $this->points[] = new Point(1, 0);
        $this->points[] = new Point(2, 0);
        $this->points[] = new Point(3, 0);

        $this->points2 = new Points();
        $this->points2[] = new Point(6, 1);
        $this->points2[] = new Point(7, 1);
        $this->points2[] = new Point(8, 1);
    }

    public function testGetDistance()
    {
        $c = new RoundEarthDistanceCalc();
        $this->assertEqualsWithDelta(111 /* km */, $c->getDistance($this->points[0], $this->points[1]) / 1000, 1.0);
    }

    public function testCalcPointsDistance()
    {
        $c = new RoundEarthDistanceCalc();
        $this->assertEqualsWithDelta(222 /* km */, $c->calcPointsDistance($this->points) / 1000, 1.0);
    }

    public function testCalcTrackDistance()
    {
        $c = new RoundEarthDistanceCalc();
        $t = new Track();
        $t->appendSegment((new Segment())->setPoints($this->points));
        $t->appendSegment((new Segment())->setPoints($this->points2));
        $this->assertEqualsWithDelta(444 /* km */, $c->calcTrackDistance($t) / 1000, 1.0);
    }
}
