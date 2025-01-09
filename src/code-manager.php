<?php declare(strict_types=1);

namespace Aircury\CodeManager;

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;

$application = new Application();

$application->run();
