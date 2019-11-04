<?php declare(strict_types=1);

namespace Intminds\GPS;

class Segment implements \IteratorAggregate
{
    /**
     * @var string
     */
    protected $title;
    /**
     * @var Points
     */
    protected $points;

    public function __construct($title = "")
    {
        $this->title = $title;
        $this->points = new Points();
    }

    /**
     * @return \Traversable
     */
    public function getIterator()
    {
        yield "title" => $this->title;
        yield "points" => $this->points;
    }

    public function setTitle(string $title): Segment
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setPoints(Points $points): Segment
    {
        $this->points = $points;
        return $this;
    }

    public function getPoints(): Points
    {
        return $this->points;
    }

    public function getStart(): ?Point
    {
        return $this->points->getStart();
    }

    public function getFinish(): ?Point
    {
        return $this->points->getFinish();
    }

    public function appendPoint(Point $point): Segment
    {
        $this->points->appendPoint($point);
        return $this;
    }
}