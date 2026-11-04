<?php declare(strict_types=1);

// tomasvotruba/cognitive-complexity lives in this repository's vendor/ or, once installed, next to this package.
$extension = '/tomasvotruba/cognitive-complexity/config/extension.neon';

foreach ([__DIR__.'/vendor', __DIR__.'/../..'] as $vendorDir) {
    if (file_exists($vendorDir.$extension)) {
        return ['includes' => [realpath($vendorDir.$extension)]];
    }
}

throw new LogicException('tomasvotruba/cognitive-complexity is not installed.');
