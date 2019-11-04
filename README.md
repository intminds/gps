# gps
Lib for parsing .gpx files and working with GPS tracks.

The main goal of this library is to be extendable.
Almost all algorithms are implemented as strategies and can be replaced (distance calculation, elevation calculation, smoothing filters, etc.) 

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
  * `TriangleElevationFilterProcessor` smooths points' altitudes using a triangular window filter.
  * You can write more processors yourself.
* 100% test coverage.

### Not implemented

* <span style="color: #600;">Support of .gpx Trails and Waypoints (`<rte>` and `<wpt>` tags).</span>
* <span style="color: #600;">Creating .gpx file back from GPXFile, Track, Segment and so on.</span> 
* <span style="color: #600;">Smoothing of lat/lng.</span>
 