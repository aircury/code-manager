<?php declare(strict_types=1);

namespace Aircury\PHPStan\Tests\Rules;

use Aircury\PHPStan\Rules\FileLengthRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<FileLengthRule>
 */
final class FileLengthRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new FileLengthRule(10);
    }

    public function testFileAtLimitPasses(): void
    {
        $this->analyse([__DIR__.'/../Fixtures/FileAtLimit.php'], []);
    }

    public function testFileOverLimitFails(): void
    {
        $this->analyse([__DIR__.'/../Fixtures/FileOverLimit.php'], [
            ['File has 11 lines, maximum allowed is 10.', 11],
        ]);
    }
}
