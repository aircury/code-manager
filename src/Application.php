<?php declare(strict_types=1);

namespace Aircury\CodeManager;

use Aircury\CodeManager\CodeAnalyser\CodeAnalyserCommand;
use Aircury\CodeManager\CodeFormatter\CodeFormatterCommand;
use Aircury\CodeManager\NamespaceChecker\NamespaceCheckerCommand;
use Aircury\CodeManager\YamlFormatter\YamlFormatterCommand;
use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    private const string NAME = 'Code Manager';
    private const array COMMANDS = [
        CodeFormatterCommand::class,
        CodeAnalyserCommand::class,
        NamespaceCheckerCommand::class,
        YamlFormatterCommand::class,
    ];

    public function __construct()
    {
        parent::__construct(name: self::NAME);

        $this->registerCommands();
    }

    private function registerCommands(): void
    {
        foreach (self::COMMANDS as $commandClass) {
            $commandClass = new $commandClass();

            $this->add($commandClass);
        }
    }
}
