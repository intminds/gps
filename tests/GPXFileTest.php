<?php
declare(strict_types=1);

namespace Intminds\GPS;

use PHPUnit\Framework\TestCase;

final class GPXFileTest extends TestCase
{
    public function testBasic()
    {
        $f = GPXFile::createFromFile(dirname(__FILE__) . "/1.gpx");
        $this->assertTrue($f->isOK());
        $t = $f->getTracks();
        $this->assertSame(2, count($t));
        $this->assertSame("Day 1", $t[0]->getTitle());
        $this->assertSame(2, count($t[0]->getSegments()));
        $this->assertSame("Part 1", $t[0]->getSegments()[0]->getTitle());
        $this->assertSame(3, count($t[0]->getSegments()[0]->getPoints()));
        $start = $t[0]->getStart();
        $this->assertEqualsWithDelta(1.0, $start->lat, 0.01);
        $this->assertEqualsWithDelta(0.0, $start->lng, 0.01);
        $this->assertEqualsWithDelta(10.0, $start->alt, 0.01);
        $this->assertSame(1262347200, $start->time); //2010-01-01T12:00:00Z
    }

    public function testFileBadXml()
    {
        $f = GPXFile::createFromFile(dirname(__FILE__) . "/bad-xml.gpx");
        $this->assertFalse($f->isOK());
        $this->assertSame(GPXFile::ERROR_PARSE_XML, $f->getErrorCode());
    }

    public function testFileUnexistedFile()
    {
        $f = GPXFile::createFromFile(dirname(__FILE__) . "/unexisted.gpx");
        $this->assertFalse($f->isOK());
        $this->assertSame(GPXFile::ERROR_NO_FILE, $f->getErrorCode());
    }

    public function testStringBadXml()
    {
        $f = GPXFile::createFromString('<?xml version="1.0" encoding="UTF-8"?><gpx>');
        $this->assertFalse($f->isOK());
        $this->assertSame(GPXFile::ERROR_PARSE_XML, $f->getErrorCode());
    }

    public function testEmptyFile()
    {
        $f = GPXFile::createFromString('<?xml version="1.0" encoding="UTF-8"?><gpx/>');
        $this->assertTrue($f->isOK());
        $this->assertSame(0, count($f->getTracks()));
    }

    public function testFlatten()
    {
        $f = GPXFile::createFromFile(dirname(__FILE__) . "/1.gpx");
        $t = $f->flatten();
        $this->assertSame(3, count($t->getSegments()));
        $this->assertSame("Day 1 / Part 1", $t->getSegments()[0]->getTitle());
        $this->assertSame("Day 1 / Part 2", $t->getSegments()[1]->getTitle());
        $this->assertSame("Day 2", $t->getSegments()[2]->getTitle());
    }
}
