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
    const AWS_REGION = 'us-west-2';

    // From https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-rekognition-2016-06-27.html
    const AWS_REKOGNITION_VERSION = '2016-06-27';

    /**
     * Detect both text and labels
     * @var string
     */
    const DETECT_BOTH = 'detect_both';

    /**
     * Detect text only
     * @var string
     */
    const DETECT_TEXT = 'detect_text';

    /**
     * Detect labels only
     * @var string
     */
    const DETECT_LABEL = 'detect_label';

    /** @var string */
    protected $detectMode;

    /** @var RekognitionClient */
    protected $rekognitionClient;

    /** @var LabelService */
    protected $labelService;

    /** @var TextService */
    protected $textService;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $options = $this->buildOptions($options);

        $this->detectMode = self::DETECT_BOTH;

        $this->rekognitionClient = new RekognitionClient($options);
        $this->setLabelService(new LabelService($this->rekognitionClient));
        $this->setTextService(new TextService($this->rekognitionClient));
    }

    /**
     * Set detection mode, default is to detect both
     * labels and text.
     *
     * @param string $mode
     */
    public function setDetectMode($mode): void
    {
        $this->detectMode = $mode;
    }

    /**
     * @param string $binaryContent
     * @return Image
     */
    public function detect(string $binaryContent): Image
    {
        $rekognitionImage = new Image;
        $rekognitionImage->setBinaryContent($binaryContent);

        return $this->handleDetection($rekognitionImage);
    }

    /**
     * @param string $s3Url
     * @return Image
     */
    public function detectFromS3(string $s3Url): Image
    {
        $rekognitionImage = new Image;
        $rekognitionImage->setUrlContent($s3Url);

        return $this->handleDetection($rekognitionImage);
    }

    /**
     * @param string $url
     * @return Image
     * @throws Exception
     */
    public function detectFromUrl(string $url): Image
    {
        if (strpos($url, 's3.amazonaws.com') !== false) {
            return $this->detectFromS3($url);
        }
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

    /**
     * @param array $options
     * @return array
     */
    protected function buildOptions(array $options): array
    {
        $defaultOptions = [
            'region' => self::AWS_REGION,
            'version' => self::AWS_REKOGNITION_VERSION,
        ];

        $environmentOptions = $this->getEnvironmentOptions();

        $options = array_merge($defaultOptions, $environmentOptions, $options);

        return $options;
    }

    /**
     * @return array
     */
    protected function getEnvironmentOptions(): array
    {
        $environmentOptions = [];

        $region = getenv('AWS_REGION');
        if ($region !== false) {
            $environmentOptions['region'] = $region;
        }

        $version = getenv('AWS_REKOGNITION_VERSION');
        if ($version !== false) {
            $environmentOptions['version'] = $version;
        }

        return $environmentOptions;
    }

    /**
     * Run detection, respecting mode
     * @param Image $image
     * @return Image
     */
    private function handleDetection(Image $image): Image
    {
        if ($this->detectMode == self::DETECT_BOTH || $this->detectMode == self::DETECT_LABEL) {
            $this->labelService->detectLabels($image);
        }
        if ($this->detectMode == self::DETECT_BOTH || $this->detectMode == self::DETECT_TEXT) {
            $this->textService->detectText($image);
        }

        return $image;
    }
}
