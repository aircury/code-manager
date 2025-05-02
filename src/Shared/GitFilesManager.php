<?php declare(strict_types=1);

namespace Aircury\CodeManager\Shared;

class GitFilesManager
{
    /**
     * @return array<string>
     */
    public static function getCurrentChangedFiles(): array
    {
        exec("git diff --relative --name-only HEAD | grep -E '.*\.(php|yaml|yml)$' | cat", output: $changedFiles);

        return $changedFiles;
    }

    /**
     * @return array<string>
     */
    public static function getCurrentBranchFiles(string $baseBranch): array
    {
        exec("git diff --relative --name-only $baseBranch... | grep -E '.*\.(php|yaml|yml)$' | cat", output: $changedFiles);

        return $changedFiles;
    }
}
