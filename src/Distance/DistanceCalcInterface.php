<?php
declare(strict_types=1);

namespace Intminds\GPS\Distance;

use Intminds\GPS\Points;
use Intminds\GPS\Track;

interface DistanceCalcInterface
{
    public function calcPointsDistance(Points $points): float;

    public function calcTrackDistance(Track $track): float;
}