<?php

declare(strict_types=1);

namespace ClickAndMortar\Rekognition\Service;

use Aws\Rekognition\RekognitionClient;
use ClickAndMortar\Rekognition\Image;
use ClickAndMortar\Rekognition\Text;

/**
 * Class TextService
 */
class TextService
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
    public function detectText(Image $rekognitionImage): void
    {
        $result = $this->getResult($rekognitionImage);

        $texts = [];
        for ($n = 0; $n < sizeof($result['TextDetections']); $n++) {
            $text = new Text();
            $text->setDetectedText($result['TextDetections'][$n]['DetectedText']);
            $text->setConfidence($result['TextDetections'][$n]['Confidence']);
            $text->setType($result['TextDetections'][$n]['Type']);

            $texts[] = $text;
        }

        $rekognitionImage->setTexts($texts);
    }

    /**
     * @param Image $rekognitionImage
     * @return \Aws\Result
     */
    protected function getResult(Image $rekognitionImage)
    {
        if ($rekognitionImage->getBinaryContent()) {
            return $this->rekognitionClient->detectText(
                [
                    'Image' => [
                        'Bytes' => $rekognitionImage->getBinaryContent(),
                    ],
                    'Attributes' => ['ALL']
                ]
            );
        }

        $url = $rekognitionImage->getUrlContent();
        $parts = explode('s3.amazonaws.com', $url);
        if (!count($parts)) {
            throw new \Exception('Unable to parse S3 url');
        }

        $bucket = trim(substr($parts[1], 0, strpos($parts[1], '/', 1)), '/');
        $name = substr($parts[1], (strpos($parts[1], '/', 1) + 1));

        return $this->rekognitionClient->detectLabels(
            [
                'Image' => [
                    'S3Object' => [
                        'Bucket' => $bucket,
                        'Name' => $name
                    ]
                ],
                'Attributes' => ['ALL']
            ]
        );
    }
}
