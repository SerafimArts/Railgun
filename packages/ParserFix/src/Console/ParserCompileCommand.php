<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Parser\Console;

use Railt\Parser\Generator\Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ParserCompileCommand
 */
class ParserCompileCommand extends Command
{
    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'parser:compile';
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription(): string
    {
        return 'A command to generate GraphQL lexer and parser by its grammar';
    }

    /**
     * {@inheritDoc}
     * @throws \Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>Generating</comment>');

        $generator = new Generator();
        $generator->generateBuilder();

        $output->writeln('<info>Generated</info>');
    }
}
