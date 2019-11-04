# gps
Lib for parsing .gpx files and working with GPS tracks.

The main goal of this library is to be extendable.
Almost all algorithms are implemented as strategies and can be replaced (i.e. distance calculation, elevation calculation, smoothing filters, etc.) 

## Basic usage example

```php
declare(strict_types=1);

namespace Intminds\GPS;

require_once "../vendor/autoload.php";

$file = GPXFile::createFromFile(dirname(__FILE__) . "/run.gpx");
$track = $file->flatten();

echo "Title: {$track->getTitle()}\n";
// Title: Evening Run

echo "Distance: {$track->calcDistance()} m\n";
// Distance: 5897.7224760213 m

echo "Segment count: " . sizeof($track->getSegments()) . "\n";
// Segment count: 1

echo "Point count: " . sizeof($track->getSegments()[0]->getPoints()) . "\n";
// Point count: 1484

$ele = $track->calcElevation();
echo "Elevation gain: {$ele->elevationGain} m, loss: {$ele->elevationLoss} m\n";
$borders = $track->calcBorders();
// Elevation gain: 324.1 m, loss: 325.9 m

echo "Track bounding rect:\n";
echo "- latitude in [{$borders->minLat}, {$borders->maxLat}]\n";
echo "- longitude in [{$borders->minLng}, {$borders->maxLng}]\n";
// Track bounding rect:
// - latitude in [55.935543, 55.951048]
// - longitude in [-3.17725, -3.158218]

echo "Start point: ({$track->getStart()->lat}, {$track->getStart()->lng})\n";
echo "- altitude = {$track->getStart()->alt} m\n";
echo "- time = " . date("Y-m-d H:i:s", $track->getStart()->time) . "\n";
// Start point: (55.935731, -3.169383)
// - altitude = 72.8 m
// - time = 2017-04-28 18:36:32

$movement = Defaults::getMovementCalc()->getDistance($track->getStart(), $track->getFinish());
echo "How far the finish point is from the start point: {$movement} m";
// How far the finish point is from the start point: 21.724416911272 m
```