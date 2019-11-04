<?php
declare(strict_types=1);

namespace Intminds\GPS\Movement;

use Intminds\GPS\Point;

interface MovementCalcInterface
{
    public function getDistance(Point $from, Point $to): float;
}