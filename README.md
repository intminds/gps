# gps

[![Build Status](https://travis-ci.org/intminds/gps.svg?branch=master)](https://travis-ci.org/intminds/gps)
[![Coverage Status](https://coveralls.io/repos/github/intminds/gps/badge.svg?branch=master)](https://coveralls.io/github/intminds/gps?branch=master)

Lib for parsing .gpx files and working with GPS tracks.

The main goal of this library is to be extendable.
Almost all algorithms are implemented as strategies and can be replaced (distance calculation, elevation calculation, smoothing filters, etc.) 

## Requirements

PHP 7.2+ (tested on 7.2 and 7.3).

## Installation

`composer require intminds/gps:"dev-master@dev"`

Stable version is coming soon...

## Features

* Parsing tracks (`<trk>` tags) from .gpx file.
* Support of multiple tracks in one gpx file.
* Flattening (converting multiple tracks with multiple segments into one track with multiple segments).
* Support of track titles and segment titles (segment titles - in [GPS Track Editor](http://www.gpstrackeditor.com/) format because there are no segment titles in the [original spec](https://www.topografix.com/gpx.asp)).
* Start and Finish points for tracks and segments.
* Bounding rectangle calculation for a track.
* Distance calculation (1 algorithm provided, you can write your own).
* Elevation calculation (2 algorithms provided, you can write your own).
* Support of "processors" which can assign properties to track points (like distance, direction, speed), add and delete points from a track. Now implemented:
  * `DistanceProcessor` assigns a "distance" property to each track point (distance from the start point).
  * `ThinOutProcessor` removes points which are too close to each other (used to reduce a track size without significant information loss). 
  * `TriangularElevationFilterProcessor` smooths points' altitudes using a triangular window filter.
  * You can write more processors yourself.
* Track, Segment, Points and Point classes implement \Traversable which can be used for easy conversion into JSON-compatible format.
* 100% test coverage.

### Not implemented

* &#10060; Support of .gpx Trails and Waypoints (`<rte>` and `<wpt>` tags).
* &#10060; Creating .gpx file back from GPXFile, Track, Segment and so on. 
* &#10060; Smoothing of lat/lng for better distance calculation.
* Statistics calculation (distance, elevation, etc.) can be applied only to Track and Segment/Points levels. No statistics for the whole GPX track if it has more than 1 track. Nevertheless, you can flatten tracks (convert all track into one track with multiple segments using GPXFile::flatten()) and calculate stats for the resulting track.

We recommend using https://github.com/Sibyx/phpGPX if you need any of the above. Nevertheless, the phpGPX lib looks for us less extendable if you need to implement your own math.

## Basic usage example

Run `php examples/basic.php` 

```php
declare(strict_types=1);

namespace Intminds\GPS;

require_once "../vendor/autoload.php";

$file = GPXFile::createFromFile(dirname(__FILE__) . "/run.gpx");
$track = $file->flatten();

echo "Title: {$track->getTitle()}\n\n";
// Title: Evening Run

echo "Distance: {$track->calcDistance()} m\n\n";
// Distance: 5897.7224760213 m

echo "Number of segments: " . sizeof($track->getSegments()) . "\n";
echo "Number of points: " . sizeof($track->getSegments()[0]->getPoints()) . "\n\n";
// Number of segments: 1
// Number of points: 1484

$ele = $track->calcElevation();
echo "Elevation gain: {$ele->elevationGain} m, loss: {$ele->elevationLoss} m\n\n";
$borders = $track->calcBorders();
// Elevation gain: 324.1 m, loss: 325.9 m

echo "Track bounding rect:\n";
echo "- latitude in [{$borders->minLat}, {$borders->maxLat}]\n";
echo "- longitude in [{$borders->minLng}, {$borders->maxLng}]\n\n";
// Track bounding rect:
// - latitude in [55.935543, 55.951048]
// - longitude in [-3.17725, -3.158218]

echo "Start point: ({$track->getStart()->lat}, {$track->getStart()->lng})\n";
echo "- altitude = {$track->getStart()->alt} m\n";
echo "- time = " . date("Y-m-d H:i:s", $track->getStart()->time) . "\n\n";
// Start point: (55.935731, -3.169383)
// - altitude = 72.8 m
// - time = 2017-04-28 18:36:32

$movement = Defaults::getMovementCalc()->getDistance($track->getStart(), $track->getFinish());
echo "How far the finish point is from the start point: {$movement} m\n\n";
// How far the finish point is from the start point: 21.724416911272 m
```

## Elevation calculation

There is a naive approach for elevation gain calculation. You can compare every point's altitude with the previous point's altitude, and if the difference is greater than zero, add it into the total elevation gain.

Such naive approach does not work well. If you go/run/ride along a flat surface, small non-important altitude changes sum up into big elevation gain and loss.

Our experiments show that this is a no-so-bad algorithm for elevation calculation which can be implemented using our library (run `php examples/elevation.php`):

```php
declare(strict_types=1);

namespace Intminds\GPS;

use Intminds\GPS\Elevation\ElementaryElevationCalc;
use Intminds\GPS\Elevation\HysteresisElevationCalc;
use Intminds\GPS\Processors\DistanceProcessor;
use Intminds\GPS\Processors\TriangularElevationFilterProcessor;

require_once "../vendor/autoload.php";

$file = GPXFile::createFromFile(dirname(__FILE__) . "/run.gpx");
$track = $file->flatten();

// NAIVE APPROACH
$ele = $track->calcElevation(new ElementaryElevationCalc());
echo "Elevation gain (naive approach): {$ele->elevationGain} m, loss: {$ele->elevationLoss} m\n";
// Elevation gain (naive approach): 324.1 m, loss: 325.9 m

// ADVANCED APPROACH
// Assigning a distance from the start to each point.
// This is required by TriangularElevationFilterProcessor.
// If it's not done, MissingPropException is raised.
$proc1 = new DistanceProcessor();
$track->applyProcessor($proc1);
// Smoothing data with triangular window filter (triangle base length is 60m)
$proc2 = new TriangularElevationFilterProcessor($windowSize = 60.0);
$track->applyProcessor($proc2);
// Applying a special elevation calculator which omits any altitude variations which are less than $minimalChange.
$ele = $track->calcElevation(new HysteresisElevationCalc($minimalChange = 2.0));
echo "Elevation gain (advanced approach): {$ele->elevationGain} m, loss: {$ele->elevationLoss} m\n";
// Elevation gain (advanced approach): 245.37668979092 m, loss: 247.98170342056 m
```

Algorithm adequacy check:

For the `examples/run.gpx` file a popular app Strava gives elevation gain 229 m. It's slightly less than we received above (245 m). You can play with the parameters but in our opinion Strava does too aggressive smoothing which lessens the total elevation gain. The values `$windowSize = 60.0` and `$minimalChange = 2.0` are based on comparison of 5 different tracks' elevations with Strava and manual elevation gain calculations for some tracks.    

