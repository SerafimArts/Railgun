<?php
/**
 * This file is part of Railgun package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Railgun\Tests\Compiler;

use PHPUnit\Framework\Assert;
use Serafim\Railgun\Compiler\Compiler;
use Serafim\Railgun\Tests\AbstractTestCase;
use Serafim\Railgun\Tests\Support\SpecTest;
use Serafim\Railgun\Tests\Support\SpecSupport;
use PHPUnit\Framework\ExpectationFailedException;
use Serafim\Railgun\Compiler\Exceptions\UnexpectedTokenException;

/**
 * Class CompilerTestCase
 * @package Serafim\Railgun\Tests\Compiler
 * @group large
 */
class AstTestCase extends AbstractTestCase
{
    use SpecSupport;

    /**
     * @var string
     */
    protected $specDirectory = __DIR__ . '/../.resources/ast-spec-tests';

    /**
     * @dataProvider specProvider
     * @param SpecTest $spec
     * @throws UnexpectedTokenException
     */
    public function testSpecs(SpecTest $spec): void
    {
        echo 'Spec: ' . $spec->getName() .
            "\n    defined in " . $spec->getPath() . "\n";
        flush();

        $compiler = new Compiler();

        $document = $compiler->parse($spec->getIn());
        $dump = trim($document->dump());

        try {
            $otherwise = 'Error in test "' . str_replace('"', "'", $spec->getName())
                . '" defined in ' . $spec->getPath();

            Assert::assertEquals($spec->getOut(), $dump, $otherwise);
        } catch (ExpectationFailedException $e) {
            echo $this->specDiff($spec, $dump);
            flush();
            throw $e;
        }
    }
}
