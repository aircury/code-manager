<?php declare(strict_types=1);

namespace Aircury\CodeManager\CodeFormatter;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CodeFormatterManager
{
    private const string FORMATTER_BASE_COMMAND = 'vendor/friendsofphp/php-cs-fixer/php-cs-fixer';
    private const string FORMAT_COMMAND = 'fix';
    private const string READ_ONLY_FORMAT_COMMAND = 'check';
    private const string DIFF_COMMAND_OPTION = '--diff';
    private const string CONFIG_FILE_COMMAND_OPTION = '--config';
    private const string QUIET_COMMAND_OPTION = '--quiet';
    private const string COMMAND_SEPARATOR = ' ';
    private const string CONFIGURATION_FILE = 'src/CodeFormatter/CodeFormatterConfiguration.php';

    /**
     * @param array<string> $files
     */
    public static function getCommand(InputInterface $input, OutputInterface $output, array $files): string
    {
        $filesArgument = implode(self::COMMAND_SEPARATOR, $files);

        $baseCommand = self::getBaseCommand($input);

        $commandOptions = self::getCommandOptions($output);

        return implode(self::COMMAND_SEPARATOR, [$baseCommand, $commandOptions, $filesArgument]);
    }

    private static function getBaseCommand(InputInterface $input): string
    {
        $formatterBaseCommand = self::FORMATTER_BASE_COMMAND;

        $commandAction = self::FORMAT_COMMAND;

        $readOnlyOption = $input->getOption(CodeFormatterCommandConfigurator::READ_ONLY_OPTION);

        if (true === $readOnlyOption) {
            $commandAction = self::READ_ONLY_FORMAT_COMMAND;
        }

        return implode(self::COMMAND_SEPARATOR, [$formatterBaseCommand, $commandAction]);
    }

    private static function getCommandOptions(OutputInterface $output): string
    {
        $commandOptions = [];

        $commandOptions[] = sprintf('%s=%s', self::CONFIG_FILE_COMMAND_OPTION, self::CONFIGURATION_FILE);

        if ($output->isQuiet()) {
            $commandOptions[] = self::QUIET_COMMAND_OPTION;
        }

        if ($output->isVerbose()) {
            $commandOptions[] = self::DIFF_COMMAND_OPTION;
        }

        return implode(self::COMMAND_SEPARATOR, $commandOptions);
    }
}
