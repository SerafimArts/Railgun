<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Lexer;

use Railt\Compiler\Lexer\Stream\Stream;
use Railt\Io\Readable;

/**
 * Interface LexerInterface
 */
interface LexerInterface
{
    /**
     * @param Readable $input
     * @return Stream
     */
    public function read(Readable $input): Stream;
}
