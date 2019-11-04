<?php declare(strict_types=1);

namespace Intminds\GPS\Calc;

use Intminds\GPS\Flatten\DefaultFlattenCalc;
use Intminds\GPS\Segment;
use Intminds\GPS\Track;
use PHPUnit\Framework\TestCase;

final class DefaultFlattenCalcTest extends TestCase
{
    public function testBasic()
    {
        $tracks = [];
        $tracks[] = new Track("A");
        $tracks[] = (new Track("B"))->appendSegment(new Segment("S1"));
        $tracks[] = (new Track("C"))->appendSegment(new Segment("S2"))->appendSegment(new Segment("S3"));
        $f = new DefaultFlattenCalc();
        $this->assertSame(["A", "B", "C / S2", "C / S3"], $this->getSegmentTitles($f->flattenTracks($tracks)));
    }

    private function getSegmentTitles(Track $track)
    {
        $result = [];
        foreach ($track->getSegments() as $segment) {
            $result[] = $segment->getTitle();
        }
        return $result;
    }

    public function testSingleTrack()
    {
        $track = new Track("A");
        $tracks = [$track];
        $f = new DefaultFlattenCalc();
        $this->assertSame($track, $f->flattenTracks($tracks));
    }

    public function testEmpty()
    {
        $tracks = [];
        $f = new DefaultFlattenCalc();
        $this->assertTrue($f->flattenTracks($tracks) instanceof Track);
    }
}