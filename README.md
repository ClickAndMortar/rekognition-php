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

![](img/tshirt.png) ![](img/demo.png)

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

The following code will allow to retrieve the output from
previous picture:

```php
<?php

use ClickAndMortar\Rekognition\Service\DetectService;

require 'vendor/autoload.php';

$filename = 'img/demo.png';
$filePointerImage = fopen($filename, 'r');
$image = fread($filePointerImage, filesize($filename));
fclose($filePointerImage);

$detectService = new DetectService();

$rekognitionImage = $detectService->detect($image);
$minimumConfidence = 80;

print 'Labels:' . PHP_EOL;
foreach ($rekognitionImage->getLabels($minimumConfidence) as $label) {
    print $label;
}

print 'Texts:' . PHP_EOL;
foreach ($rekognitionImage->getTexts($minimumConfidence) as $text) {
    print $text;
}

```

Output will be the same if:

```php
$rekognitionImage = $detectService->detect($image);
```

is replaced with:

```php
$url = 'https://raw.githubusercontent.com/ClickAndMortar/rekognition-php/master/img/demo.png';
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
