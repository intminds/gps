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

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setPoints(Points $points): void
    {
        $this->points = $points;
    }

    public function getPoints(): Points
    {
        return $this->points;
    }
}