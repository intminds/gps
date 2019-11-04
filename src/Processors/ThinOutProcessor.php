<?php
declare(strict_types=1);

namespace Intminds\GPS\Processors;

use Intminds\GPS\Defaults;
use Intminds\GPS\Movement\MovementCalcInterface;
use Intminds\GPS\Points;

class ThinOutProcessor extends AbstractProcessor implements ProcessorInterface
{
    /**
     * @var float
     */
    protected $minDistance;
    /**
     * @var MovementCalcInterface
     */
    protected $movementCalc;

    public function __construct(float $minDistance = 15.0, MovementCalcInterface $movementCalc = null)
    {
        $this->minDistance = $minDistance;
        $this->movementCalc = $movementCalc ?: Defaults::getMovementCalc();
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
            if ($this->movementCalc->getDistance($lastPoint, $point) >= $this->minDistance) {
                $newPoints[] = $point;
                $lastPoint = $point;
            }
        }
        $newPoints[] = $points[$count - 1];
        $points->replaceWith($newPoints);
    }
}