<?php declare(strict_types=1);

namespace Aircury\CodeManager\CodeAnalyser;

use Aircury\CodeManager\Shared\CodeToolManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'code:analyser')]
class CodeAnalyserCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setDescription('Run code analyser')
            ->setHelp('This command allows you to analyse your code')
        ;

        CodeAnalyserCommandConfigurator::configureCommand($this);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $files = $this->getCommandFiles($input);

            $command = CodeAnalyserManager::getCommand($input, $output, $files);

            $exitCode = CodeToolManager::executeCommand($command);

            if (0 !== $exitCode) {
                return $exitCode;
            }
        } catch (\Exception $exception) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * @return array<string>
     */
    private function getCommandFiles(InputInterface $input): array
    {
        $files = CodeToolManager::getCommandFiles($input);

        if (empty($files)) {
            throw new \LogicException('Analyser could not find any files to format');
        }

        return $files;
    }
}
