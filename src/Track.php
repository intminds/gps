<?php
declare(strict_types=1);

namespace Intminds\GPS;

class Track
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var Segment[]
     */
    protected $segments = [];

    public function __construct($title = "")
    {
        $this->title = $title;
    }

    public function setTitle(string $title): Track
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function appendSegment(Segment $segment): Track
    {
        $this->segments[] = $segment;
        return $this;
    }

    /**
     * @return Segment[]
     */
    public function getSegments(): array
    {
        return $this->segments;
    }

    public function getStart(): ?Point
    {
        foreach ($this->segments as $segment) {
            if ($segment->getStart()) {
                return $segment->getStart();
            }
        }
        return null;
    }

    public function getFinish(): ?Point
    {
        foreach (array_reverse($this->segments) as $segment) {
            if ($segment->getFinish()) {
                return $segment->getFinish();
            }
        }
        return null;
    }
}