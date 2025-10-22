<?php declare(strict_types=1);

$includes = [];
$baseConfig = __DIR__ . '/phpstan.neon.base_config.php';
$baseProjectDir = getcwd();
$baselineFile = $baseProjectDir . '/phpstan-baseline.neon';

require $baseConfig;

if (file_exists($baselineFile))
{
    $includes[] = $baselineFile;
}

$parameters['reportUnmatchedIgnoredErrors'] = false;

return [
    'includes' => $includes,
    'parameters' => $parameters,
];

