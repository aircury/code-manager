<?php declare(strict_types=1);

namespace Aircury\CodeManager;

require_once __DIR__ . '/../vendor/autoload.php';

use Aircury\CodeManager\CodeAnalyser\CodeAnalyserCommand;
use Aircury\CodeManager\CodeFormatter\CodeFormatterCommand;
use Symfony\Component\Console\Application;

$application = new Application();

$formatterCommand = new CodeFormatterCommand();

$application->add($formatterCommand);

$analyserCommand = new CodeAnalyserCommand();

$application->add($analyserCommand);

$application->run();
