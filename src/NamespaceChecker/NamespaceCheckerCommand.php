<?php

namespace Aircury\CodeManager\NamespaceChecker;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class NamespaceCheckerCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('namespace:check')
            ->setDescription('Check if classes follow PSR-4 autoloading and namespace rules')
            ->addArgument(
                'project-root',
                InputArgument::OPTIONAL,
                'The root directory of the project to analyze',
                '.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $projectRoot = $this->resolveProjectRoot($input->getArgument('project-root'));

        $io->writeln("Analyzing PSR-4 compliance for project: {$projectRoot}");

        $composerData = $this->loadComposerData($projectRoot);
        if (!$composerData) {
            $io->error('Could not load composer.json from project root');
            return Command::FAILURE;
        }

        $psr4Mappings = $this->extractPsr4Mappings($composerData);
        if (empty($psr4Mappings)) {
            $io->warning('No PSR-4 mappings found in composer.json');
            return Command::SUCCESS;
        }

        $violations = $this->analyzePhpFiles($psr4Mappings, $projectRoot, $io);

        $this->reportResults($violations, $io);

        return empty($violations) ? Command::SUCCESS : Command::FAILURE;
    }

    private function resolveProjectRoot(string $projectRoot): string
    {
        if ($projectRoot === '.') {
            return getcwd();
        }

        if (!str_starts_with($projectRoot, '/')) {
            return getcwd() . '/' . $projectRoot;
        }

        return $projectRoot;
    }

    private function loadComposerData(string $projectRoot): ?array
    {
        $composerPath = $projectRoot . '/composer.json';
        
        if (!file_exists($composerPath)) {
            return null;
        }

        $content = file_get_contents($composerPath);
        return json_decode($content, true);
    }

    private function extractPsr4Mappings(array $composerData): array
    {
        return $composerData['autoload']['psr-4'] ?? [];
    }

    private function analyzePhpFiles(array $psr4Mappings, string $projectRoot, SymfonyStyle $io): array
    {
        $violations = [];
        $allFiles = $this->findAllPhpFiles($psr4Mappings, $projectRoot);
        $totalFiles = count($allFiles);

        foreach ($allFiles as $file) {
            $applicableMapping = $this->findApplicableMapping($file, $psr4Mappings, $projectRoot);
            
            if (!$applicableMapping) {
                continue;
            }

            $violation = $this->checkFileCompliance(
                $file, 
                $applicableMapping['namespace'], 
                $applicableMapping['directory']
            );
            
            if ($violation) {
                $violations[] = $violation;
            }
        }

        $io->writeln("Analyzed {$totalFiles} PHP files");

        return $violations;
    }

    private function findAllPhpFiles(array $psr4Mappings, string $projectRoot): array
    {
        $allFiles = [];
        $processedDirs = [];

        foreach ($psr4Mappings as $directory) {
            $fullDirectory = $projectRoot . '/' . ltrim($directory, '/');
            $fullDirectory = rtrim($fullDirectory, '/');
            $realPath = realpath($fullDirectory);
            
            if (!$realPath || in_array($realPath, $processedDirs)) {
                continue;
            }
            
            $processedDirs[] = $realPath;
            $files = $this->findPhpFiles($fullDirectory);
            $allFiles = array_merge($allFiles, $files);
        }

        return array_unique($allFiles);
    }

    private function findApplicableMapping(string $filePath, array $psr4Mappings, string $projectRoot): ?array
    {
        $bestMatch = null;
        $longestPath = 0;

        foreach ($psr4Mappings as $namespacePrefix => $directory) {
            $fullDirectory = $projectRoot . '/' . ltrim($directory, '/');
            $fullDirectory = rtrim($fullDirectory, '/');
            $realDirectory = realpath($fullDirectory);
            
            if (!$realDirectory) {
                continue;
            }

            $realFilePath = realpath($filePath);
            
            if (str_starts_with($realFilePath, $realDirectory)) {
                $pathLength = strlen($realDirectory);
                
                if ($pathLength > $longestPath) {
                    $longestPath = $pathLength;
                    $bestMatch = [
                        'namespace' => $namespacePrefix,
                        'directory' => $fullDirectory
                    ];
                }
            }
        }

        return $bestMatch;
    }

    private function findPhpFiles(string $directory): array
    {
        if (!is_dir($directory)) {
            return [];
        }

        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    private function checkFileCompliance(string $filePath, string $namespacePrefix, string $baseDirectory): ?array
    {
        $actualNamespace = $this->extractNamespaceFromFile($filePath);
        
        if (!$actualNamespace) {
            return [
                'file' => $filePath,
                'issue' => 'No namespace declaration found',
                'expected' => 'N/A',
                'actual' => 'N/A'
            ];
        }

        $expectedNamespace = $this->calculateExpectedNamespace($filePath, $namespacePrefix, $baseDirectory);
        
        if ($actualNamespace !== $expectedNamespace) {
            return [
                'file' => $filePath,
                'issue' => 'Namespace mismatch',
                'expected' => $expectedNamespace,
                'actual' => $actualNamespace
            ];
        }

        return null;
    }

    private function extractNamespaceFromFile(string $filePath): ?string
    {
        $content = file_get_contents($filePath);
        
        if (preg_match('/^namespace\s+([^;]+);/m', $content, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    private function calculateExpectedNamespace(string $filePath, string $namespacePrefix, string $baseDirectory): string
    {
        $relativePath = $this->getRelativePath($filePath, $baseDirectory);
        $pathParts = explode('/', dirname($relativePath));
        
        $namespaceParts = array_filter($pathParts, fn($part) => $part !== '.');
        
        $namespacePrefix = rtrim($namespacePrefix, '\\');
        
        if (empty($namespaceParts)) {
            return $namespacePrefix;
        }

        return $namespacePrefix . '\\' . implode('\\', $namespaceParts);
    }

    private function getRelativePath(string $filePath, string $baseDirectory): string
    {
        $realFilePath = realpath($filePath);
        $realBaseDirectory = realpath($baseDirectory);
        
        return substr($realFilePath, strlen($realBaseDirectory) + 1);
    }

    private function reportResults(array $violations, SymfonyStyle $io): void
    {
        if (empty($violations)) {
            $io->success('All files comply with PSR-4 namespace rules!');
            return;
        }

        $io->error(sprintf('Found %d PSR-4 compliance violations:', count($violations)));

        $tableData = [];
        foreach ($violations as $violation) {
            $tableData[] = [
                $violation['file'],
                $violation['issue'],
                $violation['expected'],
                $violation['actual']
            ];
        }

        $io->table(
            ['File', 'Issue', 'Expected Namespace', 'Actual Namespace'],
            $tableData
        );
    }
}
