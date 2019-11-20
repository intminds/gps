<?php
declare(strict_types=1);

namespace Intminds\GPS;

use Intminds\GPS\Elevation\ElementaryElevationCalc;
use Intminds\GPS\Elevation\ThresholdElevationCalc;
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
$ele = $track->calcElevation(new ThresholdElevationCalc($minimalChange = 2.0));
echo "Elevation gain (advanced approach): {$ele->elevationGain} m, loss: {$ele->elevationLoss} m\n";
// Elevation gain (advanced approach): 245.37668979092 m, loss: 247.98170342056 m
