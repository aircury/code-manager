<?php declare(strict_types=1);

$possiblePaths = [
    __DIR__ . '/../../vendor/phpstan/phpstan-doctrine/extension.neon',
    __DIR__ . '/../../../../phpstan/phpstan-doctrine/extension.neon',
];

$includes = array_map(
    static fn(string $path) => realpath($path),
    array_values(array_filter(
        $possiblePaths,
        static fn(string $path) => file_exists($path)
    ))
);

$parameters = [
    'level' => 'max',
];

return [
    'includes' => $includes,
    'parameters' => $parameters,
];
