<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Parser;

use Railt\Compiler\Exceptions\UnexpectedTokenException;
use Railt\Compiler\Exceptions\UnrecognizedTokenException;
use Railt\Compiler\Kernel\CallStack;
use Railt\Compiler\Profiler;
use Railt\Parser\Exception\UnexpectedToken;
use Railt\Parser\Exception\UnrecognizedToken;
use Railt\Parser\Llk\Parser as LlkParser;
use Railt\Parser\Llk\TreeNode;
use Railt\Reflection\Filesystem\ReadableInterface;

/**
 * Class AbstractParser
 */
abstract class AbstractParser implements ParserInterface
{
    /**
     * @var LlkParser
     */
    protected $parser;

    /**
     * @var Profiler
     */
    private $profiler;

    /**
     * @var CallStack
     */
    private $stack;

    /**
     * Parser constructor.
     * @param CallStack $stack
     */
    public function __construct(CallStack $stack)
    {
        $this->stack    = $stack;
        $this->parser   = $this->createParser();
        $this->profiler = new Profiler($this->parser);
    }

    /**
     * @return LlkParser
     */
    abstract protected function createParser(): LlkParser;

    /**
     * @param TreeNode $ast
     * @return string
     */
    public function dump(TreeNode $ast): string
    {
        return $this->profiler->dump($ast);
    }

    /**
     * @param string $sources
     * @return string
     * @throws UnrecognizedToken
     */
    public function tokens(string $sources): string
    {
        return $this->profiler->tokens($sources);
    }

    /**
     * @return string
     */
    public function trace(): string
    {
        return $this->profiler->trace();
    }

    /**
     * @param ReadableInterface $file
     * @return TreeNode
     * @throws \Railt\Compiler\Exceptions\UnexpectedTokenException
     * @throws \Railt\Parser\Exception\LexerException
     * @throws \Railt\Parser\Exception\Exception
     * @throws UnrecognizedTokenException
     */
    public function parse(ReadableInterface $file): TreeNode
    {
        try {
            return $this->parser->parse($file->getContents());
        } catch (UnexpectedToken $e) {
            throw new UnexpectedTokenException($e->getMessage(), $this->stack);
        } catch (UnrecognizedToken $e) {
            throw new UnrecognizedTokenException($e->getMessage(), $this->stack);
        }
    }
}
