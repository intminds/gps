<?php
declare(strict_types=1);

namespace Intminds\GPS\Borders;

use Intminds\GPS\Borders;
use Intminds\GPS\Track;

interface BordersCalcInterface
{
    public function calcTrackBorders(Track $track): Borders;
}