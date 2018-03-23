<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Tests\Compiler;

use Railt\Compiler\Grammar\Reader;
use Railt\Io\File;
use Symfony\Component\Finder\Finder;

/**
 * Class GrammarTestCase
 */
class GrammarTestCase extends AbstractCompilerTestCase
{
    /**
     * @return array
     */
    public function provider(): array
    {
        $result = [];

        $files = (new Finder())->files()->name('*.xml')->in(__DIR__ . '/.resources');

        /** @var \SplFileInfo $file */
        foreach ($files->getIterator() as $file) {
            $name = \array_first(\explode('.', $file->getFilename()));

            $result[] = [
                __DIR__ . '/.resources/' . $name . '.grammar.pp',
                __DIR__ . '/.resources/' . $name . '.source.txt',
                \file_get_contents(__DIR__ . '/.resources/' . $name . '.ast.xml'),
            ];
        }

        return $result;
    }

    /**
     * @dataProvider provider
     * @param string $grammar
     * @param string $sources
     * @param string $ast
     * @return void
     */
    public function testAst(string $grammar, string $sources, string $ast): void
    {
        $reader = (new Reader())->read(File::fromPathname($grammar));

        $nodes = $reader->getParser()->parse(File::fromPathname($sources));

        $this->assertSame($ast, (string)$nodes);
    }
}
