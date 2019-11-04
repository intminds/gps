<?php
declare(strict_types=1);

namespace Intminds\GPS\Flatten;

use Intminds\GPS\Track;

interface FlattenCalcInterface
{
    /**
     * @param Track[] $tracks
     * @return Track
     */
    public function flattenTracks(array $tracks): Track;
}