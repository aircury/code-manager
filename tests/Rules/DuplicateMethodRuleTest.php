<?php declare(strict_types=1);

namespace Aircury\PHPStan\Tests\Rules;

use Aircury\PHPStan\Collectors\MethodBodyHashCollector;
use Aircury\PHPStan\Rules\DuplicateMethodRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<DuplicateMethodRule>
 */
final class DuplicateMethodRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new DuplicateMethodRule();
    }

    protected function getCollectors(): array
    {
        return [new MethodBodyHashCollector()];
    }

    public function testDuplicateGroupAcrossTwoFiles(): void
    {
        // shortBody() is identical too but has 4 statements, under the default minimum of 5.
        $this->analyse([__DIR__.'/../Fixtures/DuplicateA.php', __DIR__.'/../Fixtures/DuplicateB.php'], [
            ['Identical method bodies: Aircury\PHPStan\Tests\Fixtures\DuplicateA::sum(), Aircury\PHPStan\Tests\Fixtures\DuplicateB::total().', 10],
        ]);
    }
}
