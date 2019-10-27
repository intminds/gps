<?php
declare(strict_types=1);

namespace Intminds\GPS;

use Intminds\GPS\Calc\DistanceCalcInterface;

class Points implements \IteratorAggregate, \Countable, \ArrayAccess
{
    /**
     * @var Point[]
     */
    protected $points = [];
    /**
     * @var Point
     */
    protected $start = null;
    /**
     * @var Point
     */
    protected $finish = null;

    /**
     * @return \Traversable|Point[]
     */
    public function getIterator()
    {
        yield from $this->points;
    }

    public function count()
    {
        return sizeof($this->points);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->points[$offset]);
    }

    public function offsetGet($offset): ?Point
    {
        return $this->points[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        if (!$value instanceof Point) {
            $type = gettype($value);
            $offsetInfo = is_numeric($offset) ? " (offset {$offset})" : "";
            throw new \UnexpectedValueException("Instance of Point expected, {$type} received{$offsetInfo}");
        }
        if (is_null($offset)) {
            $this->points[] = $value;
        } elseif ($offset <= sizeof($this->points)) {
            $this->points[$offset] = $value;
        } else {
            throw new \OutOfRangeException("Indices of Points must not have gaps");
        }
    }

    public function offsetUnset($offset): void
    {
        if ($offset === sizeof($this->points) - 1) {
            unset($this->points[$offset]);
        } else {
            throw new \OutOfRangeException("Indices of Points must not have gaps");
        }
    }

    public function getStart(): ?Point
    {
        return $this->start;
    }

    public function getFinish(): ?Point
    {
        return $this->finish;
    }

    public function appendPoint(Point $point): Points
    {
        $this->points[] = $point;
        if (is_null($this->start)) {
            $this->start = $point;
        }
        $this->finish = $point;
        return $this;
    }

    public function fillDistances(DistanceCalcInterface $distanceCalc, float $startDistance = 0): Points
    {
        $distanceCalc->fillPointsDistances($this, $startDistance);
        return $this;
    }

    public function hasDistancesFilled(): bool
    {
        return is_null($this->finish) || !is_null($this->finish->distance);
    }
}