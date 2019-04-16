<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Component\Io\Exception;

use Railt\Component\Io\PositionInterface;
use Railt\Component\Io\Readable;

/**
 * Interface ExternalExceptionInterface
 */
interface ExternalExceptionInterface extends PositionInterface
{
    /**
     * @param Readable $file
     * @param int $offsetOrLine
     * @param int|null $column
     * @return ExternalExceptionInterface
     */
    public function throwsIn(Readable $file, int $offsetOrLine = 0, int $column = null): self;
}