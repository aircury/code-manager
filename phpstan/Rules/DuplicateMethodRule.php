<?php declare(strict_types=1);

namespace Aircury\PHPStan\Rules;

use Aircury\PHPStan\Collectors\MethodBodyHashCollector;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * One error per group of identical method bodies, reported at the first location (by file, then line).
 *
 * @implements Rule<CollectedDataNode>
 */
final class DuplicateMethodRule implements Rule
{
    public function getNodeType(): string
    {
        return CollectedDataNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $groups = [];

        foreach ($node->get(MethodBodyHashCollector::class) as $file => $entries) {
            foreach ($entries as [$hash, $method, $line]) {
                // Keyed by location: a trait method is collected once per using class.
                $groups[$hash][$file.':'.$line] ??= ['file' => $file, 'line' => $line, 'method' => $method];
            }
        }

        $errors = [];

        foreach ($groups as $locations) {
            if (\count($locations) < 2) {
                continue;
            }

            usort($locations, static fn (array $a, array $b): int => [$a['file'], $a['line']] <=> [$b['file'], $b['line']]);

            $errors[] = RuleErrorBuilder::message(\sprintf(
                'Identical method bodies: %s.',
                implode(', ', array_column($locations, 'method')),
            ))
                ->identifier('aircury.duplicateMethod')
                ->file($locations[0]['file'])
                ->line($locations[0]['line'])
                ->build();
        }

        return $errors;
    }
}
