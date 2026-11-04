<?php declare(strict_types=1);

namespace Aircury\PHPStan\Tests\Fixtures;

class DuplicateA
{
    /**
     * @param array<int> $values
     */
    public function sum(array $values): int
    {
        // Comments do not matter.
        $total = 0;
        $count = \count($values);
        foreach ($values as $value) {
            $total += $value;
        }
        $total *= 2;

        return $total + $count;
    }

    public function shortBody(int $a): int
    {
        $b = $a + 1;
        $c = $b + 1;
        $d = $c + 1;

        return $d;
    }

    public function differentBody(int $a): int
    {
        $b = $a + 1;
        $c = $b + 1;
        $d = $c + 1;
        $e = $d + 1;

        return $e;
    }
}
