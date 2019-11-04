<?php
declare(strict_types=1);

namespace Intminds\GPS\Elevation;

use Intminds\GPS\ElevationTotal;
use Intminds\GPS\Points;

class ElementaryElevationCalc extends AbstractElevationCalc
{
    public function calcPointsElevation(Points $points): ElevationTotal
    {
        $result = new ElevationTotal();
        $count = sizeof($points);
        for ($i = 0; $i < $count - 1; ++$i) {
            $gain = $points[$i + 1]->alt - $points[$i]->alt;
            if ($gain >= 0) {
                $result->elevationGain += $gain;
            } else {
                $result->elevationLoss += (-$gain);
            }
        }
        return $result;
    }
}