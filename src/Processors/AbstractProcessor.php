<?php
declare(strict_types=1);

namespace Intminds\GPS\Processors;

use Intminds\GPS\Points;
use Intminds\GPS\Track;

abstract class AbstractProcessor implements ProcessorInterface
{
    abstract protected function applyToPoints(Points $points): void;

    public function applyToTrack(Track $track): void
    {
        foreach ($track->getSegments() as $segment) {
            $this->applyToPoints($segment->getPoints());
        }
    }
}