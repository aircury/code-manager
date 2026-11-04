<?php declare(strict_types=1);

namespace Aircury\PHPStan\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Covers methods, functions, closures and arrow functions: they all implement FunctionLike.
 * Each one is measured on its own; nested closures and classes do not count toward the parent.
 *
 * @implements Rule<FunctionLike>
 */
final class CyclomaticComplexityRule implements Rule
{
    private const array DECISION_NODES = [
        Stmt\If_::class,
        Stmt\ElseIf_::class,
        Stmt\For_::class,
        Stmt\Foreach_::class,
        Stmt\While_::class,
        Stmt\Do_::class,
        Stmt\Catch_::class,
        Expr\Ternary::class,
        Expr\BinaryOp\Coalesce::class,
        Expr\BinaryOp\BooleanAnd::class,
        Expr\BinaryOp\BooleanOr::class,
        Expr\BinaryOp\LogicalAnd::class,
        Expr\BinaryOp\LogicalOr::class,
    ];

    public function __construct(private readonly int $maxComplexity = 10)
    {
    }

    public function getNodeType(): string
    {
        return FunctionLike::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $complexity = 1 + self::countDecisions($node->getStmts() ?? []);

        if ($complexity <= $this->maxComplexity) {
            return [];
        }

        return [
            RuleErrorBuilder::message(\sprintf(
                '%s has cyclomatic complexity of %d, maximum allowed is %d.',
                self::describe($node, $scope),
                $complexity,
                $this->maxComplexity,
            ))
                ->identifier('aircury.cyclomaticComplexity')
                ->build(),
        ];
    }

    /**
     * NodeFinder cannot skip subtrees, so this walks the AST by hand and stops at nested functions and classes.
     *
     * @param array<mixed> $nodes
     */
    private static function countDecisions(array $nodes): int
    {
        $count = 0;

        foreach ($nodes as $node) {
            if (!$node instanceof Node || $node instanceof FunctionLike || $node instanceof Stmt\ClassLike) {
                continue;
            }

            $count += (int) self::isDecision($node);

            foreach ($node->getSubNodeNames() as $name) {
                $subNode = $node->{$name};
                $count += self::countDecisions(\is_array($subNode) ? $subNode : [$subNode]);
            }
        }

        return $count;
    }

    private static function isDecision(Node $node): bool
    {
        return \in_array($node::class, self::DECISION_NODES, true)
            || ($node instanceof Stmt\Case_ && null !== $node->cond)
            || ($node instanceof Node\MatchArm && null !== $node->conds);
    }

    private static function describe(FunctionLike $node, Scope $scope): string
    {
        return match (true) {
            $node instanceof Stmt\ClassMethod => \sprintf(
                'Method %s::%s()',
                $scope->getClassReflection()?->getDisplayName() ?? 'class@anonymous',
                $node->name->toString(),
            ),
            $node instanceof Stmt\Function_ => \sprintf(
                'Function %s()',
                ($node->namespacedName ?? $node->name)->toString(),
            ),
            $node instanceof Expr\ArrowFunction => 'Arrow function',
            default => 'Closure',
        };
    }
}
