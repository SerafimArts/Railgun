<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Runtime\Ast;

/**
 * Interface LeafInterface
 */
interface LeafInterface extends NodeInterface
{
    /**
     * @return string
     */
    public function getValue(): string;

    /**
     * @return int
     */
    public function getOffset(): int;
}
