<?php

declare(strict_types=1);

namespace ClickAndMortar\Rekognition\Service;

use Aws\Rekognition\RekognitionClient;
use ClickAndMortar\Rekognition\Image;
use ClickAndMortar\Rekognition\Label;

/**
 * Class LabelService
 */
class LabelService
{
    /** @var RekognitionClient */
    private $rekognitionClient;

    /**
     * @param RekognitionClient $rekognitionClient
     */
    public function __construct(RekognitionClient $rekognitionClient)
    {
        $this->rekognitionClient = $rekognitionClient;
    }

    /**
     * @param Image $rekognitionImage
     */
    public function detectLabels(Image $rekognitionImage): void
    {
        $result = $this->getResult($rekognitionImage);

        $labels = [];
        for ($n = 0; $n < sizeof($result['Labels']); $n++) {
            $label = new Label();
            $label->setName($result['Labels'][$n]['Name']);
            $label->setConfidence($result['Labels'][$n]['Confidence']);

            $parents = [];
            foreach ($result['Labels'][$n]['Parents'] as $parent) {
                $parents[] = $parent['Name'];
            }

            $label->setParents($parents);
            $labels[] = $label;
        }

        $rekognitionImage->setLabels($labels);
    }

    /**
     * @param Image $rekognitionImage
     * @return \Aws\Result
     */
    protected function getResult(Image $rekognitionImage)
    {
        return $this->rekognitionClient->detectLabels(
            [
                'Image' => [
                    'Bytes' => $rekognitionImage->getBinaryContent(),
                ],
                'Attributes' => ['ALL']
            ]
        );
    }
}
