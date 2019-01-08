<?php

declare(strict_types=1);

namespace ClickAndMortar\Rekognition;

/**
 * Class Label
 */
class Label
{
    /** @var string */
    private $name;

    /** @var float */
    private $confidence;

    /** @var string[] */
    private $parents = [];

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
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
     * @return string[]
     */
    public function getParents(): array
    {
        return $this->parents;
    }

    /**
     * @param string[] $parents
     */
    public function setParents(array $parents): void
    {
        $this->parents = $parents;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $string =
            'Name: ' . $this->getName() . PHP_EOL
            . 'Confidence: ' . $this->getConfidence() . PHP_EOL;

        foreach ($this->getParents() as $parent) {
            $string .= 'Parent Name: ' . $parent . PHP_EOL;
        }

        $string .= PHP_EOL;

        return $string;
    }
}
