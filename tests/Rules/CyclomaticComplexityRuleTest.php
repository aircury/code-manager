<?php declare(strict_types=1);

namespace Aircury\PHPStan\Tests\Rules;

use Aircury\PHPStan\Rules\CyclomaticComplexityRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<CyclomaticComplexityRule>
 */
final class CyclomaticComplexityRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new CyclomaticComplexityRule();
    }

    public function testComplexity(): void
    {
        $this->analyse([__DIR__.'/../Fixtures/Complexity.php'], [
            // atLimit() (10) passes; the method holding the closures (1) and the anonymous class (10) pass too.
            ['Method Aircury\PHPStan\Tests\Fixtures\Complexity::overLimit() has cyclomatic complexity of 11, maximum allowed is 10.', 12],
            ['Closure has cyclomatic complexity of 11, maximum allowed is 10.', 19],
            ['Arrow function has cyclomatic complexity of 11, maximum allowed is 10.', 22],
            ['Function Aircury\PHPStan\Tests\Fixtures\everyDecision() has cyclomatic complexity of 17, maximum allowed is 10.', 40],
        ]);
    }
}
