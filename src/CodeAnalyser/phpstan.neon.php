<?php declare(strict_types=1);

$possiblePaths = [
    '../../vendor/phpstan/phpstan-doctrine/extension.neon',
    '../../../../phpstan/phpstan-doctrine/extension.neon',
];

$includes = array_filter($possiblePaths, static fn (string $path) => file_exists($path));

$parameters = [
    'level' => 'max',
];

$config = [
    'includes' => $includes,
    'parameters' => $parameters,
];

return $config;
