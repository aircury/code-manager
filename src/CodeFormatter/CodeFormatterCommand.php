<?php declare(strict_types=1);

namespace Aircury\CodeManager\CodeFormatter;

use Aircury\CodeManager\Shared\CodeToolManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'code:format')]
class CodeFormatterCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setDescription('Run code formatter')
            ->setHelp('This command allows you to format your code')
        ;

        CodeFormatterCommandConfigurator::configureCommand($this);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $files = $this->getCommandFiles($input);

            $command = CodeFormatterManager::getCommand($input, $output, $files);

            CodeToolManager::executeCommand($command);
        } catch (\Exception $exception) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }

        $io->success('Code formatter run successfully');

        return Command::SUCCESS;
    }

    /**
     * @return array<string>
     */
    private function getCommandFiles(InputInterface $input): array
    {
        $files = CodeToolManager::getCommandFiles($input);

        if (empty($files)) {
            throw new \LogicException('Formatter could not find any files to format');
        }

        return $files;
    }
}
