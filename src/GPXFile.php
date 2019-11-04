<?php
declare(strict_types=1);

namespace Intminds\GPS;

use Intminds\GPS\Flatten\FlattenCalcInterface;

class GPXFile
{
    const ERROR_OK = "";
    const ERROR_PARSE_XML = "ERROR_PARSE_XML";
    const ERROR_NO_FILE = "ERROR_NO_FILE";

    protected $errorCode = self::ERROR_OK;
    protected $filePath;
    protected $fileContents;
    /**
     * @var \SimpleXMLElement
     */
    protected $simpleXml;
    /**
     * @var Track[]
     */
    protected $tracks = [];

    public static function createFromFile(string $filePath): self
    {
        return new self($filePath);
    }

    public static function createFromString(string $fileContents): self
    {
        return new self(null, $fileContents);
    }

    protected function __construct($filePath = null, $fileContents = null)
    {
        $this->filePath = $filePath;
        $this->fileContents = $fileContents;
        $this->parseXml();
        $this->isOK() && $this->parseTracks();
    }

    public function isOK(): bool
    {
        return self::ERROR_OK === $this->errorCode;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    protected function parseXml(): void
    {
        $libXmlOptions = LIBXML_NOERROR | LIBXML_NONET | LIBXML_NOWARNING;
        if (is_null($this->fileContents)) {
            if (!file_exists($this->filePath)) {
                $this->errorCode = self::ERROR_NO_FILE;
                return;
            }
            $this->simpleXml = simplexml_load_file($this->filePath, "SimpleXMLElement", $libXmlOptions);
        } else {
            $this->simpleXml = simplexml_load_string($this->fileContents, "SimpleXMLElement", $libXmlOptions);
        }
        if (false === $this->simpleXml) {
            $this->errorCode = self::ERROR_PARSE_XML;
        }
    }

    protected function parseTracks(): void
    {
        $ns = $this->simpleXml->getNamespaces(true);
        foreach ($this->simpleXml->trk as $trkNode) {
            $track = new Track((string)$trkNode->name);
            foreach ($trkNode->trkseg as $trksegNode) {
                $segmentName = "";
                if (isset($ns["gte"])) {
                    $trksegExtNodes = $trksegNode->extensions->children($ns["gte"]);
                    $segmentName = (string)$trksegExtNodes->name;
                }
                $segment = new Segment($segmentName);
                foreach ($trksegNode->trkpt as $trkptNode) {
                    $alt = !!$trkptNode->ele ? (float)$trkptNode->ele : null;
                    $time = !!$trkptNode->time ? strtotime((string)$trkptNode->time) : null;
                    $segment->appendPoint(new Point((float)$trkptNode["lat"], (float)$trkptNode["lon"], $alt, $time));
                }
                $track->appendSegment($segment);
            }
            $this->tracks[] = $track;
        }
    }

    /**
     * @return Track[]
     */
    public function getTracks(): array
    {
        return $this->tracks;
    }

    public function flatten(FlattenCalcInterface $flattenCalc = null): Track
    {
        if (is_null($flattenCalc)) {
            $flattenCalc = Defaults::getFlattenCalc();
        }
        $resultTrack = $flattenCalc->flattenTracks($this->tracks);
        $this->tracks = [$resultTrack];
        return $resultTrack;
    }
}