<?php
declare(strict_types=1);

namespace Intminds\GPS;

require_once "../vendor/autoload.php";

$file = GPXFile::createFromFile(dirname(__FILE__) . "/run.gpx");
$track = $file->flatten();

echo "Title: {$track->getTitle()}\n";

echo "Distance: {$track->calcDistance()} m\n";

echo "Segment count: " . sizeof($track->getSegments()) . "\n";

echo "Point count: " . sizeof($track->getSegments()[0]->getPoints()) . "\n";

$ele = $track->calcElevation();
echo "Elevation gain: {$ele->elevationGain} m, loss: {$ele->elevationLoss} m\n";
$borders = $track->calcBorders();

echo "Track bounding rect:\n";
echo "- latitude in [{$borders->minLat}, {$borders->maxLat}]\n";
echo "- longitude in [{$borders->minLng}, {$borders->maxLng}]\n";

echo "Start point: ({$track->getStart()->lat}, {$track->getStart()->lng})\n";
echo "- altitude = {$track->getStart()->alt} m\n";
echo "- time = " . date("Y-m-d H:i:s", $track->getStart()->time) . "\n";

$movement = Defaults::getMovementCalc()->getDistance($track->getStart(), $track->getFinish());
echo "How far the finish point is from the start point: {$movement} m";
