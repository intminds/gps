<?php
declare(strict_types=1);

namespace Intminds\GPS;

class Point
{
    /**
     * @var float
     */
    public $lat;
    /**
     * @var float
     */
    public $lng;
    /**
     * @var float|null
     */
    public $alt;
    /**
     * @var int|null
     */
    public $time;
    /**
     * @var float|null
     */
    public $distance;

    public function __construct(float $lat, float $lng, ?float $alt = 0, int $time = null, float $distance = null)
    {
        $this->lat = $lat;
        $this->lng = $lng;
        $this->alt = is_null($alt) ? 0 : $alt;
        $this->time = $time;
        $this->distance = $distance;
    }
}