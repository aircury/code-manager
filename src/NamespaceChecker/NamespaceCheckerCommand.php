<?php

namespace Aircury\CodeManager\NamespaceChecker;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

class NamespaceCheckerCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('namespace:check')
            ->setDescription('Check if classes follow PSR-4 autoloading and namespace rules');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $process = new Process(['composer', 'dump-autoload', '--optimize', '--strict-psr']);
        $process->setTimeout(null);
        
        $io->writeln('Running composer dump-autoload and checking namespaces...');
        
        $process->run(
            function (string $type, string $buffer) use ($io) {
                $io->write($buffer);
            }
        );

        if (!$process->isSuccessful()) {
            $io->error('Failed to dump autoloader, check namespace and PSR-4 rules');

            return Command::FAILURE;
        }

        $io->success('Autoloader dumped and namespace checked successfully.');
        
        return $process->getExitCode();
    }
}
