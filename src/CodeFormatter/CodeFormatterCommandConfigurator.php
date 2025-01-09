<?php declare(strict_types=1);

namespace Aircury\CodeManager\CodeFormatter;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class CodeFormatterCommandConfigurator
{
    public const string FILES_ARGUMENT = 'files';
    public const string READ_ONLY_OPTION = 'read-only';
    public const string BRANCH_FORMAT_OPTION = 'branch';

    public static function configureCommand(CodeFormatterCommand $command): void
    {
        $command
            ->addArgument(self::FILES_ARGUMENT, InputArgument::IS_ARRAY, 'Specify files that you want to format')
            ->addOption(self::BRANCH_FORMAT_OPTION, 'b', InputOption::VALUE_REQUIRED, 'Run the formatter in your branch new files relative to master')
            ->addOption(self::READ_ONLY_OPTION, 'o', InputOption::VALUE_NONE, 'Run the formatter in read only mode, showing the diff if the files had been formatted and the applied rules')
        ;
    }
}
