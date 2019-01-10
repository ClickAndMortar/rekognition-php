<?php

declare(strict_types=1);

namespace ClickAndMortar\Rekognition;

/**
 * Class Image
 */
class Image
{
    /** @var string */
    private $binaryContent;

    /** @var Label[] */
    private $labels = [];

    /** @var Text[] */
    private $texts = [];

    /**
     * @param string $binaryContent
     */
    public function __construct(string $binaryContent)
    {
        $this->setBinaryContent($binaryContent);
    }

    /**
     * @return string
     */
    public function getBinaryContent(): string
    {
        return $this->binaryContent;
    }

    /**
     * @param string $binaryContent
     */
    public function setBinaryContent(string $binaryContent): void
    {
        $this->binaryContent = $binaryContent;
    }

    /**
     * @param int $minimumConfidence
     * @return Label[]
     */
    public function getLabels(int $minimumConfidence = 0): array
    {
        $labels = array_filter(
            $this->labels,
            function (Label $label) use ($minimumConfidence) {
                return $label->getConfidence() >= $minimumConfidence;
            }
        );

        return $labels;
    }

    /**
     * @param Label[] $labels
     */
    public function setLabels(array $labels): void
    {
        $this->labels = $labels;
    }

    /**
     * @param int $minimumConfidence
     * @param string|null $type
     * @return Text[]
     */
    public function getTexts(int $minimumConfidence = 0, string $type = null): array
    {
        $texts = array_filter(
            $this->texts,
            function (Text $text) use ($minimumConfidence, $type) {
                return $text->getConfidence() >= $minimumConfidence
                    && ($type === null || $text->getType() === $type);
            }
        );

        return $texts;
    }

    /**
     * @param Text[] $texts
     */
    public function setTexts(array $texts): void
    {
        $this->texts = $texts;
    }
}
