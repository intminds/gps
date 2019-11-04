<?php
declare(strict_types=1);

namespace Intminds\GPS\Borders;

use Intminds\GPS\Borders;
use Intminds\GPS\Points;
use Intminds\GPS\Track;

class DefaultBordersCalc implements BordersCalcInterface
{
    protected function calcPointsBorders(Points $points): Borders
    {
        $borders = new Borders();
        foreach ($points as $point) {
            if (is_null($borders->minLat)) {
                $borders->minLat = $point->lat;
                $borders->minLng = $point->lng;
                $borders->maxLat = $point->lat;
                $borders->maxLng = $point->lng;
            } else {
                if ($point->lat < $borders->minLat) {
                    $borders->minLat = $point->lat;
                } elseif ($point->lat > $borders->maxLat) {
                    $borders->maxLat = $point->lat;
                }
                if ($point->lng < $borders->minLng) {
                    $borders->minLng = $point->lng;
                } elseif ($point->lng > $borders->maxLng) {
                    $borders->maxLng = $point->lng;
                }
            }
        }
        return $borders;
    }

    public function calcTrackBorders(Track $track): Borders
    {
        $borders = new Borders();
        foreach ($track->getSegments() as $segment) {
            $segmentBorders = $this->calcPointsBorders($segment->getPoints());
            if (!is_null($segmentBorders->minLat)) {
                if (is_null($borders->minLat)) {
                    $borders = $segmentBorders;
                } else {
                    $borders->minLat = min($borders->minLat, $segmentBorders->minLat);
                    $borders->minLng = min($borders->minLng, $segmentBorders->minLng);
                    $borders->maxLat = max($borders->maxLat, $segmentBorders->maxLat);
                    $borders->maxLng = max($borders->maxLng, $segmentBorders->maxLng);
                }
            }
        }
        return $borders;
    }
}