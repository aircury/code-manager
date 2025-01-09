<?php declare(strict_types=1);

namespace Aircury\CodeManager\CodeAnalyser;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class CodeAnalyserCommandConfigurator
{
    public const string FILES_ARGUMENT = 'files';
    public const string BRANCH_FORMAT_OPTION = 'branch';
    public const string LEVEL_OPTION = 'level';

    public static function configureCommand(CodeAnalyserCommand $command): void
    {
        $command
            ->addArgument(self::FILES_ARGUMENT, InputArgument::IS_ARRAY, 'Specify files that you want to format')
            ->addOption(self::BRANCH_FORMAT_OPTION, 'b', InputOption::VALUE_REQUIRED, 'Run the formatter in your branch new files relative to master')
            ->addOption(self::LEVEL_OPTION, 'l', InputOption::VALUE_OPTIONAL, 'Specify level to run the analyser')
        ;
    }
}
