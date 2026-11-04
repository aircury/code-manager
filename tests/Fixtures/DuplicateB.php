<?php declare(strict_types=1);

namespace Aircury\PHPStan\Tests\Fixtures;

class DuplicateB
{
    /**
     * @param array<int> $numbers
     */
    public function total(array $numbers): int
    {
        $sum = 0;
        $size = \count($numbers);
        foreach ($numbers as $number) {
            /* Neither do these. */
            $sum += $number;
        }
        $sum *= 2;

        return $sum + $size;
    }

    public function shortBody(int $x): int
    {
        $y = $x + 1;
        $z = $y + 1;
        $w = $z + 1;

        return $w;
    }

    public function differentBody(int $a): int
    {
        $b = $a + 1;
        $c = $b + 1;
        $d = $c + 1;
        $e = $d - 1;

        return $e;
    }
}
