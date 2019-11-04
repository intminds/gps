<?php
declare(strict_types=1);

namespace Intminds\GPS\Distance;

use Intminds\GPS\Point;
use Intminds\GPS\Points;
use Intminds\GPS\Segment;
use Intminds\GPS\Track;
use PHPUnit\Framework\TestCase;

final class DefaultDistanceCalcTest extends TestCase
{
    /**
     * @var Points
     */
    private $points1;
    /**
     * @var Points
     */
    private $points2;

    protected function setUp()
    {
        $this->points1 = new Points();
        $this->points1[] = new Point(1, 0);
        $this->points1[] = new Point(2, 0);
        $this->points1[] = new Point(3, 0);

        $this->points2 = new Points();
        $this->points2[] = new Point(6, 1);
        $this->points2[] = new Point(7, 1);
        $this->points2[] = new Point(8, 1);
    }

    public function testCalcPointsDistance()
    {
        $c = new DefaultDistanceCalc();
        $this->assertEqualsWithDelta(222 /* km */, $c->calcPointsDistance($this->points1) / 1000, 1.0);
    }

    public function testCalcTrackDistance()
    {
        $c = new DefaultDistanceCalc();
        $t = new Track();
        $t->appendSegment((new Segment())->setPoints($this->points1));
        $t->appendSegment((new Segment())->setPoints($this->points2));
        $this->assertEqualsWithDelta(444 /* km */, $c->calcTrackDistance($t) / 1000, 1.0);
    }
}