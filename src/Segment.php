<?php declare(strict_types=1);

namespace Intminds\GPS;

class Segment
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

    public function getStart(): ?Point
    {
        return $this->points->getStart();
    }

    public function getFinish(): ?Point
    {
        return $this->points->getFinish();
    }

    public function appendPoint(Point $point): void
    {
        $this->points->appendPoint($point);
    }
}