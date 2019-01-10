<?php

declare(strict_types=1);

namespace ClickAndMortar\Rekognition\Service;

use Aws\Rekognition\RekognitionClient;
use ClickAndMortar\Rekognition\Image;
use Exception;

/**
 * Class DetectService
 */
class DetectService
{
    /** @var RekognitionClient */
    protected $rekognitionClient;

    /** @var array */
    protected $options;

    /** @var LabelService */
    protected $labelService;

    /** @var TextService */
    protected $textService;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->rekognitionClient = new RekognitionClient($options);
        $this->setLabelService(new LabelService($this->rekognitionClient));
        $this->setTextService(new TextService($this->rekognitionClient));
    }

    /**
     * @param string $binaryContent
     * @return Image
     */
    public function detect(string $binaryContent): Image
    {
        $rekognitionImage = new Image($binaryContent);

        $this->labelService->detectLabels($rekognitionImage);
        $this->textService->detectText($rekognitionImage);

        return $rekognitionImage;
    }

    /**
     * @param string $url
     * @return Image
     * @throws Exception
     */
    public function detectFromUrl(string $url): Image
    {
        $binaryContent = @file_get_contents($url);

        if ($binaryContent === false) {
            throw new Exception('Error while getting image from url: ' . $url);
        }

        return $this->detect($binaryContent);
    }

    /**
     * @param string $base64image
     * @return Image
     * @throws Exception
     */
    public function detectFromBase64(string $base64image): Image
    {
        $binaryContent = base64_decode($base64image, true);

        if ($binaryContent === false) {
            throw new Exception('Error while decoding base64 image');
        }

        return $this->detect($binaryContent);
    }

    /**
     * @return RekognitionClient
     */
    public function getRekognitionClient(): RekognitionClient
    {
        return $this->rekognitionClient;
    }

    /**
     * @param LabelService $labelService
     */
    public function setLabelService(LabelService $labelService): void
    {
        $this->labelService = $labelService;
    }

    /**
     * @param TextService $textService
     */
    public function setTextService(TextService $textService): void
    {
        $this->textService = $textService;
    }
}
