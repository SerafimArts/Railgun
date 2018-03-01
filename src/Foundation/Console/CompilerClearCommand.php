<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Foundation\Console;

use Railt\SDL\Parser\Compiled;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CompilerClearCommand
 */
class CompilerClearCommand extends Command
{
    /**
     * @return void
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configure(): void
    {
        $this->setName('compiler:clear');
        $this->setDescription('Remove optimised compiler runtime and switches the mode to direct reading of grammar file.');
    }

    /**
     * @param InputInterface $in
     * @param OutputInterface $out
     * @return int|null|void
     * @throws \LogicException
     */
    public function execute(InputInterface $in, OutputInterface $out)
    {
        if (! \class_exists(Compiled::class)) {
            return $out->writeln('<comment>Compiled sources not found</comment>');
        }

        $reflection = new \ReflectionClass(Compiled::class);
        \unlink($reflection->getFileName());

        $out->writeln('<info>OK</info>');
    }
}
