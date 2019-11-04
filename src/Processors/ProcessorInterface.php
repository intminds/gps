<?php
declare(strict_types=1);

namespace Intminds\GPS\Processors;

use Intminds\GPS\Track;

interface ProcessorInterface
{
    public function applyToTrack(Track $track): void;
}