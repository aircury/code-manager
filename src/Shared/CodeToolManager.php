<?php declare(strict_types=1);

namespace Aircury\CodeManager\Shared;

use Aircury\CodeManager\CodeAnalyser\CodeAnalyserCommandConfigurator;
use Symfony\Component\Console\Input\InputInterface;

class CodeToolManager
{
    /**
     * @return array<string>
     */
    public static function getCommandFiles(InputInterface $input): array
    {
        $argumentFiles = $input->getArgument(CodeAnalyserCommandConfigurator::FILES_ARGUMENT);

        if (\is_array($argumentFiles) && !empty($argumentFiles)) {
            self::checkFilesExists($argumentFiles);

            return $argumentFiles;
        }

        $branchArgument = $input->getOption(CodeAnalyserCommandConfigurator::BRANCH_FORMAT_OPTION);

        if (null !== $branchArgument && !\is_string($branchArgument)) {
            throw new \LogicException('Branch argument is not a string');
        }

        if (null !== $branchArgument) {
            $gitFiles = GitFilesManager::getCurrentBranchFiles($branchArgument);
        } else {
            $gitFiles = GitFilesManager::getCurrentChangedFiles();
        }

        return self::filterNonExistentFiles($gitFiles);
    }

    /**
     * @param array<string> $files
     *
     * @return array<string> $filteredFiles
     */
    private static function filterNonExistentFiles(array $files): array
    {
        return array_filter($files, static fn ($file) => file_exists($file));
    }

    /**
     * @param array<string> $files
     */
    private static function checkFilesExists(array $files): void
    {
        foreach ($files as $file) {
            if (!file_exists($file)) {
                throw new \LogicException(\sprintf('File "%s" does not exist', $file));
            }
        }
    }

    public static function executeCommand(string $command): void
    {
        $process = proc_open($command, [\STDIN, \STDOUT, \STDERR], $pipes);

        if (false !== $process) {
            proc_close($process);
        }
    }
}
