<?php
declare(strict_types=1);

namespace Intminds\GPS;

use PHPUnit\Framework\TestCase;

final class SegmentTest extends TestCase
{
    public function testStartFinish()
    {
        $s = new Segment();
        $this->assertNull($s->getStart());
        $this->assertNull($s->getFinish());
        $s->appendPoint(new Point(1.0, 2.0));
        $this->assertEqualsWithDelta(1.0, $s->getStart()->lat, 0.01);
        $this->assertEqualsWithDelta(2.0, $s->getStart()->lng, 0.01);
        $this->assertEqualsWithDelta(1.0, $s->getFinish()->lat, 0.01);
        $this->assertEqualsWithDelta(2.0, $s->getFinish()->lng, 0.01);
        $s->appendPoint(new Point(3.0, 4.0));
        $s->appendPoint(new Point(5.0, 6.0));
        $this->assertEqualsWithDelta(1.0, $s->getStart()->lat, 0.01);
        $this->assertEqualsWithDelta(2.0, $s->getStart()->lng, 0.01);
        $this->assertEqualsWithDelta(5.0, $s->getFinish()->lat, 0.01);
        $this->assertEqualsWithDelta(6.0, $s->getFinish()->lng, 0.01);
    }
}
