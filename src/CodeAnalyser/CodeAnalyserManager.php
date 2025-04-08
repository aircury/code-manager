<?php declare(strict_types=1);

namespace Aircury\CodeManager\CodeAnalyser;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CodeAnalyserManager
{
    private const string FORMATTER_BASE_COMMAND = 'vendor/phpstan/phpstan/phpstan';
    private const string ANALYSIS_COMMAND = 'analyse';
    private const string CONFIG_FILE_COMMAND_OPTION = '--configuration';
    private const string LEVEL_COMMAND_OPTION = '--level';
    private const string QUIET_COMMAND_OPTION = '--quiet';
    private const string COMMAND_SEPARATOR = ' ';
    private const string CONFIGURATION_FILE = 'phpstan.neon';

    /**
     * @param array<string> $files
     */
    public static function getCommand(InputInterface $input, OutputInterface $output, array $files): string
    {
        $filesArgument = implode(self::COMMAND_SEPARATOR, $files);

        $baseCommand = self::getBaseCommand();

        $commandOptions = self::getCommandOptions($input, $output);

        return implode(self::COMMAND_SEPARATOR, [$baseCommand, $commandOptions, $filesArgument]);
    }

    private static function getBaseCommand(): string
    {
        $formatterBaseCommand = self::FORMATTER_BASE_COMMAND;

        $commandAction = self::ANALYSIS_COMMAND;

        return implode(self::COMMAND_SEPARATOR, [$formatterBaseCommand, $commandAction]);
    }

    private static function getCommandOptions(InputInterface $input, OutputInterface $output): string
    {
        $commandOptions = [];

        $configurationFile = self::getConfigurationFile();

        $commandOptions[] = sprintf('%s=%s', self::CONFIG_FILE_COMMAND_OPTION, $configurationFile);

        if ($output->isQuiet()) {
            $commandOptions[] = self::QUIET_COMMAND_OPTION;
        }

        if ($input->hasOption(CodeAnalyserCommandConfigurator::LEVEL_OPTION)) {
            $level = $input->getOption(CodeAnalyserCommandConfigurator::LEVEL_OPTION);

            if (\is_string($level)) {
                $commandOptions[] = sprintf('%s=%s', self::LEVEL_COMMAND_OPTION, $level);
            }
        }

        return implode(self::COMMAND_SEPARATOR, $commandOptions);
    }

    private static function getConfigurationFile(): string
    {
        $configurationFile = sprintf('%s%s%s', __DIR__, DIRECTORY_SEPARATOR, self::CONFIGURATION_FILE);

        if (file_exists($configurationFile)) {
            return $configurationFile;
        }

        throw new \LogicException('Configuration file not found');
    }
}
