<?php
declare(strict_types=1);

namespace Intminds\GPS\Processors;

use Intminds\GPS\Defaults;
use Intminds\GPS\Distance\DistanceCalcInterface;
use Intminds\GPS\Points;

class ThinOutProcessor extends AbstractProcessor implements ProcessorInterface
{
    /**
     * @var float
     */
    protected $minDistance;
    /**
     * @var DistanceCalcInterface
     */
    protected $distanceCalc;

    public function __construct(float $minDistance = 15.0, DistanceCalcInterface $distanceCalc = null)
    {
        $this->minDistance = $minDistance;
        $this->distanceCalc = $distanceCalc ?: Defaults::getDistanceCalc();
    }

    protected function applyToPoints(Points $points): void
    {
        $count = sizeof($points);
        if ($count <= 2) {
            return;
        }
        $newPoints = new Points();
        $newPoints[] = $points[0];
        $lastPoint = $points[0];
        for ($i = 1; $i < $count - 1; ++$i) {
            $point = $points[$i];
            if ($this->distanceCalc->getDistance($lastPoint, $point) >= $this->minDistance) {
                $newPoints[] = $point;
                $lastPoint = $point;
            }
        }
        $newPoints[] = $points[$count - 1];
        $points->replaceWith($newPoints);
    }
}