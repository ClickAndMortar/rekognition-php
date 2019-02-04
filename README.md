# rekognition-php

Simple to use PHP library for [AWS Rekognition](https://aws.amazon.com/rekognition/).

![Rekognition PHP Logo](https://raw.githubusercontent.com/ClickAndMortar/rekognition-php/master/rekognition-php-logo.png)

`rekognition-php` allows to detect in images:

- **Labels** (using [DetectLabels](https://docs.aws.amazon.com/rekognition/latest/dg/API_DetectLabels.html))

 > Detects instances of real-world entities within an image (JPEG or PNG)
 provided as input. This includes objects like flower, tree, and table;
 events like wedding, graduation, and birthday party;
 and concepts like landscape, evening, and nature.

- **Text** (using [DetectText](https://docs.aws.amazon.com/rekognition/latest/dg/API_DetectText.html))

> Detects text in the input image and converts it into machine-readable text.

## Install

```shell
composer require clickandmortar/rekognition-php
```

## Configuration

### Configure credentials

Before using `rekognition-php`, [set credentials to make requests to Amazon Web Services](https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_credentials.html).

### Configure Rekognition client options (Optional)

Configuring Rekognition client options is optional as default values will
be used if none are set with the following methods.

[region](https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_configuration.html#cfg-region)
and [version](https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_configuration.html#cfg-version)
are configurable using environment variables `AWS_REGION` and
`AWS_REKOGNITION_VERSION`.

It is also possible to pass this options to `DetectService` constructor:
```php
$detectService = new DetectService([
    'region' => 'eu-west-1',
    'version' => '2016-06-27',
]);
```

## Usage

Using this [image](https://docs.aws.amazon.com/rekognition/latest/dg/images/text.png)
with the following code:

```php
<?php

use ClickAndMortar\Rekognition\Service\DetectService;

require 'vendor/autoload.php';

$filename = 'text.png';
$filePointerImage = fopen($filename, 'r');
$image = fread($filePointerImage, filesize($filename));
fclose($filePointerImage);

$detectService = new DetectService();

$rekognitionImage = $detectService->detect($image);
$minimumConfidence = 99;

print 'Labels:' . PHP_EOL;
foreach ($rekognitionImage->getLabels($minimumConfidence) as $label) {
    print $label;
}

print PHP_EOL;

print 'Texts:' . PHP_EOL;
foreach ($rekognitionImage->getTexts($minimumConfidence) as $text) {
    print $text;
}

```

will output:

```
Labels:
Name: Coffee Cup
Confidence: 99.859184265137
Parent Name: Cup

Name: Drink
Confidence: 99.859184265137

Name: Latte
Confidence: 99.859184265137
Parent Name: Coffee Cup
Parent Name: Cup
Parent Name: Beverage

Name: Cup
Confidence: 99.859184265137

Name: Beverage
Confidence: 99.859184265137


Texts:
DetectedText: IT'S
Confidence: 99.438377380371
Type: LINE

DetectedText: but keep
Confidence: 99.750549316406
Type: LINE

DetectedText: Smiling
Confidence: 99.789009094238
Type: LINE

DetectedText: IT'S
Confidence: 99.438377380371
Type: WORD

DetectedText: MONDAY
Confidence: 99.98274230957
Type: WORD

DetectedText: but
Confidence: 99.655982971191
Type: WORD

DetectedText: keep
Confidence: 99.845115661621
Type: WORD

DetectedText: Smiling
Confidence: 99.789009094238
Type: WORD

```

Output will be the same if

```php
$rekognitionImage = $detectService->detect($image);
```

is replaced with

```php
$url = 'https://docs.aws.amazon.com/rekognition/latest/dg/images/text.png';
$rekognitionImage = $detectService->detectFromUrl($url);
```

or

```php
$base64image = base64_encode($image);
$rekognitionImage = $detectService->detectFromBase64($base64image);
```

## Run tests

```shell
vendor/bin/phpunit
```

## Docker

### Install

```shell
docker run --rm -it -v $PWD:/app composer install
```

### Run

Create `main.php` with code from [Usage](#usage).

```shell
docker run --rm -it -e AWS_ACCESS_KEY_ID="$AWS_ACCESS_KEY_ID" -e AWS_SECRET_ACCESS_KEY="$AWS_SECRET_ACCESS_KEY" -v "$PWD":/app -w /app php:7.1-cli php main.php
```

### Run tests

```shell
docker run --rm -it -e AWS_ACCESS_KEY_ID="$AWS_ACCESS_KEY_ID" -e AWS_SECRET_ACCESS_KEY="$AWS_SECRET_ACCESS_KEY" -v "$PWD":/app -w /app php:7.1-cli vendor/bin/phpunit
```
