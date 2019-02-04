<?php

declare(strict_types=1);

namespace ClickAndMortar\Rekognition\Tests\Service;

use Aws\MockHandler;
use Aws\Result;
use ClickAndMortar\Rekognition\Label;
use ClickAndMortar\Rekognition\Service\DetectService;
use ClickAndMortar\Rekognition\Text;
use PHPUnit\Framework\TestCase;

/**
 * Class DetectServiceTest
 */
final class DetectServiceTest extends TestCase
{
    public function testDetect(): void
    {
        // From
        // https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_handlers-and-middleware.html#mock-handler
        $mock = new MockHandler();
        $mock->append($this->getDetectLabelsResult());
        $mock->append($this->getDetectTextResult());

        $detectService = new DetectService(
            ['handler' => $mock]
        );

        $rekognitionImage = $detectService->detect('binaryContent');
        $minimumConfidence = 99;

        $this->assertEquals(
            $this->getExpectedLabel(),
            $rekognitionImage->getLabels($minimumConfidence)
        );

        $this->assertEquals(
            $this->getExpectedTexts(),
            $rekognitionImage->getTexts($minimumConfidence)
        );
    }

    /**
     * @return Result
     */
    protected function getDetectLabelsResult(): Result
    {
        $DetectLabelsResult = new Result(
            [
                'Labels' => [
                    [
                        'Name' => 'Label 1',
                        'Confidence' => 99.85918426513672,
                        'Parents' => [],
                    ],
                    [
                        'Name' => 'Label 2',
                        'Confidence' => 99.85918426513672,
                        'Parents' => [
                            [
                                'Name' => 'Label 1'
                            ],
                        ],
                    ],
                ],
            ]
        );

        return $DetectLabelsResult;
    }

    /**
     * @return array
     */
    protected function getExpectedLabel(): array
    {
        $expectedLabel = [];

        $label = new Label();
        $label->setName('Label 1');
        $label->setConfidence(99.85918426513672);
        $label->setParents([]);

        $expectedLabel[] = $label;

        $label = new Label();
        $label->setName('Label 2');
        $label->setConfidence(99.85918426513672);
        $label->setParents(['Label 1']);

        $expectedLabel[] = $label;

        return $expectedLabel;
    }

    /**
     * @return Result
     */
    protected function getDetectTextResult(): Result
    {
        $DetectTextResult = new Result(
            [
                'TextDetections' => [
                    [
                        'DetectedText' => 'Line',
                        'Confidence' => 99.85918426513672,
                        'Type' => Text::TYPE_LINE,
                    ],
                    [
                        'DetectedText' => 'Word',
                        'Confidence' => 99.85918426513672,
                        'Type' => Text::TYPE_WORD,
                    ],
                ],
            ]
        );

        return $DetectTextResult;
    }

    /**
     * @return array
     */
    protected function getExpectedTexts(): array
    {
        $expectedTexts = [];

        $text = new Text();
        $text->setDetectedText('Line');
        $text->setConfidence(99.85918426513672);
        $text->setType(Text::TYPE_LINE);

        $expectedTexts[] = $text;

        $text = new Text();
        $text->setDetectedText('Word');
        $text->setConfidence(99.85918426513672);
        $text->setType(Text::TYPE_WORD);

        $expectedTexts[] = $text;

        return $expectedTexts;
    }
}
