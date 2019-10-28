<?php
declare(strict_types=1);

namespace Intminds\GPS;

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

    public function __clone()
    {
        foreach ($this->points as $idx => $point) {
            $this->points[$idx] = clone $point;
        }
    }

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
            $this->appendPoint($value);
        } elseif ($offset <= sizeof($this->points)) {
            $this->points[$offset] = $value;
            if (sizeof($this->points) - 1 === $offset) {
                $this->finish = $value;
            }
            if (0 === $offset) {
                $this->start = $value;
            }
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

    public function replaceWith(Points $points): void
    {
        $this->points = [];
        foreach ($points as $point) {
            $this->appendPoint($point);
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
        if (1 === sizeof($this->points)) {
            $this->start = $point;
        }
        $this->finish = $point;
        return $this;
    }

    public function allPointsHaveProp($propName): bool
    {
        foreach ($this->points as $point) {
            if (!isset($point[$propName]) || is_null($point[$propName])) {
                return false;
            }
        }
        return true;
    }
}