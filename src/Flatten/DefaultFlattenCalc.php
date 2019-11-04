<?php
declare(strict_types=1);

namespace Intminds\GPS\Flatten;

use Intminds\GPS\Segment;
use Intminds\GPS\Track;

class DefaultFlattenCalc implements FlattenCalcInterface
{
    protected $titleJoiner;

    public function __construct(string $titleJoiner = " / ")
    {
        $this->titleJoiner = $titleJoiner;
    }

    /**
     * @param Track[] $tracks
     * @return Track
     */
    public function flattenTracks(array $tracks): Track
    {
        switch (sizeof($tracks)) {
            case 0:
                $resultTrack = new Track();
                break;
            case 1:
                $resultTrack = $tracks[0];
                break;
            default:
                $resultTrack = $this->getSingleTrack($tracks);
        }
        return $resultTrack;
    }

    /**
     * @param Track[] $tracks
     * @return Track
     */
    protected function getSingleTrack(array $tracks): Track
    {
        $resultTrack = new Track();
        foreach ($tracks as $track) {
            $isNamedTrack = strlen($track->getTitle()) > 0;
            $isSingleSegment = 1 === sizeof($track->getSegments());
            if (0 === sizeof($track->getSegments())) {
                $resultTrack->appendSegment(new Segment($track->getTitle()));
            }
            foreach ($track->getSegments() as $segment) {
                if ($isNamedTrack) {
                    $isNamedSegment = strlen($segment->getTitle()) > 0;
                    if ($isSingleSegment || !$isNamedSegment) {
                        $segment->setTitle($track->getTitle());
                    } else {
                        $segment->setTitle($track->getTitle() . $this->titleJoiner . $segment->getTitle());
                    }
                }
                $resultTrack->appendSegment($segment);
            }
        }
        return $resultTrack;
    }
}