<?php
declare(strict_types=1);

namespace Intminds\GPS\Processors;

use Intminds\GPS\Point;
use Intminds\GPS\Points;
use Intminds\GPS\Segment;
use Intminds\GPS\Track;
use PHPUnit\Framework\TestCase;

final class TriangleElevationFilterProcessorTest extends TestCase
{
    public function testApplyToTrack()
    {
        $points1 = new Points();
        $points1[] = new Point(1, 0, 10.0);
        $points1[] = new Point(2, 0, 100.0);
        $points1[] = new Point(3, 0, 10.0);

        $points2 = new Points();
        $points2[] = new Point(6, 1, 40.0);
        $points2[] = new Point(7, 1, 50.0);
        $points2[] = new Point(8, 1, 60.0);

        $track = new Track();
        $track->appendSegment((new Segment())->setPoints($points1));
        $track->appendSegment((new Segment())->setPoints($points2));

        $proc = new DistanceProcessor();
        $proc->applyToTrack($track);
        // The triangle averaging window has a 444km base
        // Distance between points is 111km
        // => Adjacent points have averaging weight = 0.5
        $proc = new TriangleElevationFilterProcessor(444000);
        $proc->applyToTrack($track);
        $this->assertEqualsWithDelta(40, $points1[0]->alt, 0.1); // (10x1 + 100x0.5) / (1 + 0.5)
        $this->assertEqualsWithDelta(55, $points1[1]->alt, 0.1); // (100x1 + 10x0.5 + 10x0.5) / (1 + 0.5 + 0.5)
        $this->assertEqualsWithDelta(40, $points1[2]->alt, 0.1);
        $this->assertEqualsWithDelta(43.3, $points2[0]->alt, 0.1); // (40x1 + 50x0.5) / (1 + 0.5)
        $this->assertEqualsWithDelta(50, $points2[1]->alt, 0.1); // (50x1 + 40x0.5 + 60x0.5) / (1 + 0.5 + 0.5)
        $this->assertEqualsWithDelta(56.7, $points2[2]->alt, 0.1);
    }

    public function testClosePointsAveraging()
    {
        $points1 = new Points();
        $points1[] = new Point(1, 0, 10.0);
        $points1[] = new Point(1, 0, 100.0);
        $points1[] = new Point(3, 0, 10.0);

        $track = new Track();
        $track->appendSegment((new Segment())->setPoints($points1));

        $proc = new DistanceProcessor();
        $proc->applyToTrack($track);
        $proc = new TriangleElevationFilterProcessor(); // Default window size > 0
        $proc->applyToTrack($track);
        $this->assertEqualsWithDelta(55, $points1[0]->alt, 0.1);
        $this->assertEqualsWithDelta(55, $points1[1]->alt, 0.1);
        $this->assertEqualsWithDelta(10, $points1[2]->alt, 0.1);
    }

    public function testClosePointsNoAveraging()
    {
        $points1 = new Points();
        $points1[] = new Point(1, 0, 10.0);
        $points1[] = new Point(1, 0, 100.0);
        $points1[] = new Point(3, 0, 10.0);

        $track = new Track();
        $track->appendSegment((new Segment())->setPoints($points1));

        $proc = new DistanceProcessor();
        $proc->applyToTrack($track);
        $proc = new TriangleElevationFilterProcessor(0); // Zero size window disables any averaging even for identical points
        $proc->applyToTrack($track);
        $this->assertEqualsWithDelta(10, $points1[0]->alt, 0.1);
        $this->assertEqualsWithDelta(100, $points1[1]->alt, 0.1);
        $this->assertEqualsWithDelta(10, $points1[2]->alt, 0.1);
    }

    public function testException()
    {
        $points1 = new Points();
        $points1[] = new Point(1, 0, 10.0);
        $points1[] = new Point(1, 0, 100.0);
        $points1[] = new Point(3, 0, 10.0);

        $track = new Track();
        $track->appendSegment((new Segment())->setPoints($points1));

        // DistanceProcessor is not applied
        $proc = new TriangleElevationFilterProcessor();
        $this->expectException(MissingPropException::class);
        $proc->applyToTrack($track);
    }
}