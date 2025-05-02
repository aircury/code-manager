<?php declare(strict_types=1);

namespace Aircury\CodeManager\YamlFormatter;

use Aircury\CodeManager\Shared\GitFilesManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'yaml:format')]
class YamlFormatterCommand extends Command
{
    protected function configure(): void
    {
        $this->setDescription('Run YAML formatter')
            ->addOption('indent', 'i', InputOption::VALUE_REQUIRED, 'Number of spaces for indentation', YamlFormatterManager::DEFAULT_INDENT)
            ->addOption('inline', 'l', InputOption::VALUE_REQUIRED, 'Level where to switch to inline YAML', YamlFormatterManager::DEFAULT_INLINE);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $indent = (int) $input->getOption('indent');
        $inline = (int) $input->getOption('inline');

        $yamlFiles = array_filter(
            GitFilesManager::getCurrentChangedFiles(),
            fn(string $file) => str_ends_with($file, '.yaml') || str_ends_with($file, '.yml')
        );

        if (empty($yamlFiles)) {
            $io->writeln('No YAML files to format');

            return Command::SUCCESS;
        }

        $formatter = new YamlFormatterManager();

        foreach ($yamlFiles as $file) {
            $formatter->formatFile($file, $indent, $inline);

            $io->writeln(sprintf('Formatted: %s', $file));
        }

        return Command::SUCCESS;
    }
} 