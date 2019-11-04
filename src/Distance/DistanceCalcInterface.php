<?php
declare(strict_types=1);

namespace Intminds\GPS\Distance;

use Intminds\GPS\Point;
use Intminds\GPS\Points;
use Intminds\GPS\Track;

interface DistanceCalcInterface
{
    public function getDistance(Point $from, Point $to): float;

    public function calcPointsDistance(Points $points): float;

    public function calcTrackDistance(Track $track): float;
}