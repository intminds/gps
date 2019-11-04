<?php
declare(strict_types=1);

namespace Intminds\GPS\Processors;

use Intminds\GPS\Points;

class TriangleElevationFilterProcessor extends AbstractProcessor implements ProcessorInterface
{
    /**
     * @var float
     */
    protected $windowSize;

    public function __construct(float $windowSize = 60.0)
    {
        $this->windowSize = $windowSize;
    }

    protected function applyToPoints(Points $points): void
    {
        if (!$points->allPointsHaveProp("distance")) {
            throw new MissingPropException("All points must have a 'distance' property when calling this processor. Use DistanceProcessor first.");
        }
        $newPoints = new Points();
        $count = sizeof($points);
        $halfWS = $this->windowSize / 2;
        $l = 0;
        $r = -1;
        for ($i = 0; $i < $count; ++$i) {
            $point = $points[$i];
            if ($halfWS < 0.001) { // 1mm
                // No averaging
                $alt = $point->alt;
            } else {
                // Adding right points into the window
                while ($r + 1 < $count && $points[$r + 1]["distance"] <= $point["distance"] + $halfWS) {
                    ++$r;
                }
                // Removing left points from the window
                while ($l <= $r && $points[$l]["distance"] <= $point["distance"] - $halfWS) {
                    ++$l;
                }
                // Averaging
                $sum = 0;
                $div = 0;
                for ($j = $l; $j <= $r; ++$j) {
                    $d = $point["distance"] - $points[$j]["distance"];
                    $mul = 1 - abs($d) / $halfWS;
                    $sum += $points[$j]->alt * $mul;
                    $div += $mul;
                }
                $alt = $div > 0 ? $sum / $div : 0;
            }
            $newPoint = clone $point;
            $newPoint->alt = $alt;
            $newPoints->appendPoint($newPoint);
        }
        $points->replaceWith($newPoints);
    }
}