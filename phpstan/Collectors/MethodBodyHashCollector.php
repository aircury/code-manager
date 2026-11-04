<?php declare(strict_types=1);

namespace Aircury\PHPStan\Collectors;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;

/**
 * Hashes each method body, pretty-printed, with comments stripped and variables renamed
 * to positional names in order of first use ($this is kept), so bodies differing only in naming collide.
 *
 * @implements Collector<ClassMethod, array{string, string, int}>
 */
final class MethodBodyHashCollector implements Collector
{
    private readonly Standard $printer;

    public function __construct(private readonly int $minStatements = 5)
    {
        $this->printer = new Standard();
    }

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    public function processNode(Node $node, Scope $scope): ?array
    {
        if (null === $node->stmts || \count($node->stmts) < $this->minStatements) {
            return null;
        }

        $method = \sprintf(
            '%s::%s()',
            $scope->getClassReflection()?->getDisplayName() ?? 'class@anonymous',
            $node->name->toString(),
        );

        return [self::normalise($this->printer->prettyPrint($node->stmts)), $method, $node->getStartLine()];
    }

    private static function normalise(string $code): string
    {
        $names = [];
        $normalised = '';

        foreach (\PhpToken::tokenize('<?php '.$code) as $token) {
            if ($token->is([\T_COMMENT, \T_DOC_COMMENT, \T_WHITESPACE, \T_OPEN_TAG])) {
                continue;
            }

            if ($token->is(\T_VARIABLE) && '$this' !== $token->text) {
                $names[$token->text] ??= '$v'.\count($names);
                $normalised .= $names[$token->text].' ';

                continue;
            }

            $normalised .= $token->text.' ';
        }

        return sha1($normalised);
    }
}
