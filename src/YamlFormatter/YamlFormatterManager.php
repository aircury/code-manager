<?php declare(strict_types=1);

namespace Aircury\CodeManager\YamlFormatter;

use Symfony\Component\Yaml\Yaml;

class YamlFormatterManager
{
    public const int DEFAULT_INDENT = 2;
    public const int DEFAULT_INLINE = PHP_INT_MAX;

    public function format(string $content, int $indent = self::DEFAULT_INDENT, int $inline = self::DEFAULT_INLINE): string
    {
        $data = Yaml::parse($content);
        
        return Yaml::dump($data, $inline, $indent);
    }

    public function formatFile(string $file, int $indent = self::DEFAULT_INDENT, int $inline = self::DEFAULT_INLINE): void
    {
        $content = file_get_contents($file);

        if (false === $content) {
            throw new \RuntimeException('Failed to read file: ' . $file);
        }

        $formattedContent = $this->format($content, $indent, $inline);
        
        file_put_contents($file, $formattedContent);
    }
} 