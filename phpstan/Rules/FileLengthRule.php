<?php declare(strict_types=1);

namespace Aircury\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\FileNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<FileNode>
 */
final class FileLengthRule implements Rule
{
    public function __construct(private readonly int $maxLines = 800)
    {
    }

    public function getNodeType(): string
    {
        return FileNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $contents = file($scope->getFile());

        if (false === $contents) {
            throw new \RuntimeException(\sprintf('Cannot read "%s".', $scope->getFile()));
        }

        $lines = \count($contents);

        if ($lines <= $this->maxLines) {
            return [];
        }

        return [
            RuleErrorBuilder::message(\sprintf('File has %d lines, maximum allowed is %d.', $lines, $this->maxLines))
                ->identifier('aircury.fileLength')
                ->line($this->maxLines + 1)
                ->build(),
        ];
    }
}
