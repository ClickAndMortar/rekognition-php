<?php

declare(strict_types=1);

namespace ClickAndMortar\Rekognition;

/**
 * Class Text
 */
class Text
{
    const TYPE_WORD = 'WORD';
    const TYPE_LINE = 'LINE';

    /** @var string */
    private $detectedText;

    /** @var float */
    private $confidence;

    /** @var string */
    private $type;

    /**
     * @return string
     */
    public function getDetectedText(): string
    {
        return $this->detectedText;
    }

    /**
     * @param string $detectedText
     */
    public function setDetectedText(string $detectedText): void
    {
        $this->detectedText = $detectedText;
    }

    /**
     * @return float
     */
    public function getConfidence(): float
    {
        return $this->confidence;
    }

    /**
     * @param float $confidence
     */
    public function setConfidence(float $confidence): void
    {
        $this->confidence = $confidence;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $string =
            'DetectedText: ' . $this->getDetectedText() . PHP_EOL
            . 'Confidence: ' . $this->getConfidence() . PHP_EOL
            . 'Type: ' . $this->getType() . PHP_EOL;

        $string .= PHP_EOL;

        return $string;
    }
}
