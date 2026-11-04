<?php declare(strict_types=1);

namespace Aircury\PHPStan\Tests\Fixtures;

class Complexity
{
    public function atLimit(int $a): bool
    {
        return $a > 0 && $a > 1 && $a > 2 && $a > 3 && $a > 4 && $a > 5 && $a > 6 && $a > 7 && $a > 8 && $a > 9;
    }

    public function overLimit(int $a): bool
    {
        return $a > 0 && $a > 1 && $a > 2 && $a > 3 && $a > 4 && $a > 5 && $a > 6 && $a > 7 && $a > 8 && $a > 9 && $a > 10;
    }

    public function nestedFunctionsAreNotCounted(int $a): object
    {
        $closure = static function (int $b): bool {
            return $b > 0 && $b > 1 && $b > 2 && $b > 3 && $b > 4 && $b > 5 && $b > 6 && $b > 7 && $b > 8 && $b > 9 && $b > 10;
        };
        $arrow = static fn (int $b): bool => $b > 0 && $b > 1 && $b > 2 && $b > 3 && $b > 4 && $b > 5 && $b > 6 && $b > 7 && $b > 8 && $b > 9 && $b > 10;

        return new class ($closure, $arrow) {
            public function __construct(public \Closure $closure, public \Closure $arrow)
            {
            }

            public function check(int $c): bool
            {
                return $c > 0 && $c > 1 && $c > 2 && $c > 3 && $c > 4 && $c > 5 && $c > 6 && $c > 7 && $c > 8 && $c > 9;
            }
        };
    }
}

/**
 * @param array<int> $items
 */
function everyDecision(array $items, ?int $x): int
{
    $total = 0;

    if ($x > 0) {
        ++$total;
    } elseif ($x < 0) {
        --$total;
    } else {
        $total = 0;
    }

    for ($i = 0; $i < 1; ++$i) {
        ++$total;
    }

    foreach ($items as $item) {
        $total += $item;
    }

    while ($total > 100) {
        --$total;
    }

    do {
        ++$total;
    } while ($total < 0);

    switch ($x) {
        case 1:
            ++$total;
            break;
        case 2:
            --$total;
            break;
        default:
            break;
    }

    try {
        $total = intdiv($total, 1);
    } catch (\Throwable) {
        $total = 0;
    }

    $total += $x > 1 ? 1 : 0;
    $total += $x ?? 0;
    $flag = ($x === 1 || $x === 2) and ($x === 3 or $x === 4);

    return $total + (int) $flag + match ($x) {
        1 => 1,
        2, 3 => 2,
        default => 0,
    };
}
