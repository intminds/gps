<?php
declare(strict_types=1);

namespace Intminds\GPS;

use Intminds\GPS\Borders\BordersCalcInterface;
use Intminds\GPS\Distance\DistanceCalcInterface;
use Intminds\GPS\Elevation\ElevationCalcInterface;
use Intminds\GPS\Processors\ProcessorInterface;

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

    public function calcBorders(BordersCalcInterface $bordersCalc = null): Borders
    {
        if (is_null($bordersCalc)) {
            $bordersCalc = Defaults::getBordersCalc();
        }
        return $bordersCalc->calcTrackBorders($this);
    }

    public function calcDistance(DistanceCalcInterface $distanceCalc = null): float
    {
        if (is_null($distanceCalc)) {
            $distanceCalc = Defaults::getDistanceCalc();
        }
        return $distanceCalc->calcTrackDistance($this);
    }

    public function calcElevation(ElevationCalcInterface $elevationCalc = null): ElevationTotal
    {
        if (is_null($elevationCalc)) {
            $elevationCalc = Defaults::getElevationCalc();
        }
        return $elevationCalc->calcTrackElevation($this);
    }

    public function applyProcessor(ProcessorInterface $processor): void
    {
        $processor->applyToTrack($this);
    }
}