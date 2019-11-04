<?php
declare(strict_types=1);

namespace Intminds\GPS\Elevation;

use Intminds\GPS\ElevationTotal;
use Intminds\GPS\Points;
use Intminds\GPS\Track;

interface ElevationCalcInterface
{
    public function calcPointsElevation(Points $points): ElevationTotal;

    public function calcTrackElevation(Track $track): ElevationTotal;
}