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
    private const string CONFIG_FILE = 'phpstan.neon.php';
    private const string CONFIG_FILE_WITH_BASELINE = 'phpstan.neon.with_baseline.php';

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

        $baselineEnabled = !$input->getOption(CodeAnalyserCommandConfigurator::NO_BASELINE_OPTION);

        $configurationFile = self::getConfigurationFile($baselineEnabled);

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

    private static function getConfigurationFile(bool $baselineEnabled): string
    {
        $configFile = self::CONFIG_FILE;

        if ($baselineEnabled && file_exists(__DIR__ . DIRECTORY_SEPARATOR . self::CONFIG_FILE_WITH_BASELINE)) {
            $configFile = self::CONFIG_FILE_WITH_BASELINE;
        }

        $configPath = __DIR__ . DIRECTORY_SEPARATOR . $configFile;

        if (!file_exists($configPath)) {
            throw new \LogicException('Configuration file not found');
        }

        return $configPath;
    }
}
