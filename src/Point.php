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
     * @var ?float
     */
    public $alt;
    /**
     * @var ?int
     */
    public $time;
    /**
     * @var array
     */
    public $props = [];

    public function __construct(float $lat, float $lng, ?float $alt = null, ?int $time = null)
    {
        $this->lat = $lat;
        $this->lng = $lng;
        $this->alt = $alt;
        $this->time = $time;
    }

    public function __clone()
    {
        foreach ($this->props as $propName => $prop) {
            if (is_object($prop)) {
                $this->props[$propName] = clone $prop;
            }
        }
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

    public function offsetExists($propName): bool
    {
        return in_array($propName, ["lat", "lng", "alt", "time"]) || isset($this->props[$propName]);
    }

    public function offsetGet($propName)
    {
        switch ($propName) {
            case "lat":
                return $this->lat;
            case "lng":
                return $this->lng;
            case "alt":
                return $this->alt;
            case "time":
                return $this->time;
            default:
                if (isset($this->props[$propName])) {
                    return $this->props[$propName];
                } else {
                    throw new \OutOfRangeException();
                }
        }
    }

    public function offsetSet($propName, $value): void
    {
        switch ($propName) {
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
                $this->props[$propName] = $value;
        }
    }

    public function offsetUnset($propName): void
    {
        if (in_array($propName, ["lat", "lng", "alt", "time"])) {
            throw new \OutOfRangeException();
        }
        unset($this->props[$propName]);
    }
}