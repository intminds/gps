<?php
declare(strict_types=1);

namespace Intminds\GPS\Calc;

use Intminds\GPS\Distance\RoundEarthDistanceCalc;
use Intminds\GPS\Point;
use Intminds\GPS\Points;
use Intminds\GPS\Processors\ThinOutProcessor;
use Intminds\GPS\Segment;
use Intminds\GPS\Track;
use PHPUnit\Framework\TestCase;

final class ThinOutProcessorTest extends TestCase
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
        $track->appendSegment((new Segment())->setPoints(clone $points1));
        $track->appendSegment((new Segment())->setPoints(clone $points2));

        $proc = new ThinOutProcessor(15.0, new RoundEarthDistanceCalc());
        $proc->applyToTrack($track);
        $this->assertEquals($points1, $track->getSegments()[0]->getPoints());

        $proc = new ThinOutProcessor(200000.0, new RoundEarthDistanceCalc());
        $proc->applyToTrack($track);
        $this->assertEquals([$points1[0], $points1[2]], iterator_to_array($track->getSegments()[0]->getPoints()));
    }

    public function testOnePointInSegment()
    {
        $points = new Points();
        $points->appendPoint(new Point(1, 0));
        $track = new Track();
        $track->appendSegment((new Segment())->setPoints(clone $points));

        $proc = new ThinOutProcessor(15.0, new RoundEarthDistanceCalc());
        $proc->applyToTrack($track);
        $this->assertEquals($points, $track->getSegments()[0]->getPoints());
    }

    public function testTwoPointsInSegment()
    {
        $points = new Points();
        $points->appendPoint(new Point(1, 0));
        $points->appendPoint(new Point(2, 0));
        $track = new Track();
        $track->appendSegment((new Segment())->setPoints(clone $points));

        $proc = new ThinOutProcessor(200000.0, new RoundEarthDistanceCalc());
        $proc->applyToTrack($track);
        $this->assertEquals($points, $track->getSegments()[0]->getPoints());
    }
}