<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Foundation\Console;

use Railt\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Application as CliApplication;
use Railt\Config\RepositoryInterface as ConfigRepositoryInterface;
use Railt\Foundation\Console\ConfigurationRepository as ConsoleRepository;
use Railt\Foundation\Console\RepositoryInterface as ConsoleRepositoryInterface;

/**
 * @mixin ConsoleExecutableInterface
 */
trait ConsoleExecutorTrait
{
    /**
     * @var ConsoleRepositoryInterface
     */
    protected ConsoleRepositoryInterface $commands;

    /**
     * @var array|Command[]
     */
    protected array $defaultCommands = [
        \Railt\Foundation\Console\Command\ExtensionsListCommand::class,
    ];

    /**
     * @var array|Command[]
     */
    protected array $developmentCommands = [
        \Railt\Foundation\Console\Command\RepoMergeCommand::class,
        \Railt\Foundation\Console\Command\RepoSyncCommand::class,
    ];

    /**
     * @return int
     * @throws \Exception
     */
    public function cli(): int
    {
        $cli = new CliApplication('Railt Framework', $this->getVersion());

        $this->boot();

        foreach ($this->commands as $command) {
            $cli->add($command);
        }

        return $cli->run();
    }

    /**
     * @return void
     */
    abstract public function boot(): void;

    /**
     * @return string
     */
    abstract public function getVersion(): string;

    /**
     * @param ContainerInterface $app
     * @param ConfigRepositoryInterface $config
     * @return void
     */
    protected function bootConsoleExecutorTrait(
        ContainerInterface $app,
        ConfigRepositoryInterface $config
    ): void {
        $this->commands = new ConsoleRepository($app, $config);

        foreach ($this->defaultCommands as $command) {
            $this->commands->add($command);
        }

        foreach ($this->developmentCommands as $command) {
            $this->commands->add($command);
        }
    }
}
