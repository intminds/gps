<?php
declare(strict_types=1);

namespace Intminds\GPS;

class Point implements \IteratorAggregate, \ArrayAccess
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
     * @var array
     */
    public $props = [];

    public function __construct(float $lat, float $lng, ?float $alt = null, int $time = null)
    {
        $this->lat = $lat;
        $this->lng = $lng;
        $this->alt = $alt;
        $this->time = $time;
    }

    /**
     * @return \Traversable
     */
    public function getIterator()
    {
        yield "lat" => $this->lat;
        yield "lng" => $this->lng;
        yield "alt" => $this->alt;
        yield "time" => $this->time;
        yield from $this->props;
    }

    public function offsetExists($offset): bool
    {
        return in_array($offset, ["lat", "lng", "alt", "time"]) || isset($this->props[$offset]) && !is_null($this->props[$offset]);
    }

    public function offsetGet($offset)
    {
        switch ($offset) {
            case "lat":
                return $this->lat;
            case "lng":
                return $this->lng;
            case "alt":
                return $this->alt;
            case "time":
                return $this->time;
            default:
                return $this->props[$offset] ?? null;
        }
    }

    public function offsetSet($offset, $value): void
    {
        switch ($offset) {
            case "lat":
                $this->lat = $value;
                break;
            case "lng":
                $this->lng = $value;
                break;
            case "alt":
                $this->alt = $value;
                break;
            case "time":
                $this->time = $value;
                break;
            default:
                $this->props[$offset] = $value;
        }
    }

    public function offsetUnset($offset): void
    {
        if (in_array($offset, ["lat", "lng", "alt", "time"])) {
            throw new \OutOfRangeException();
        }
        unset($this->props[$offset]);
    }
}