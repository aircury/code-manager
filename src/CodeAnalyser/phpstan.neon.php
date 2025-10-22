<?php declare(strict_types=1);

$includes = [];
$parameters = [];
$baseConfig = __DIR__ . '/phpstan.neon.base_config.php';

require $baseConfig;

return [
    'includes' => $includes,
    'parameters' => $parameters,
];
