<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Compiler\Parser\Ast;

use Railt\Compiler\Parser\Debug\NodeDumper;

/**
 * Class Node
 */
abstract class Node implements NodeInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * Node constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function is(string $name): bool
    {
        return $this->name === $name;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (new NodeDumper($this))->toString();
    }
}
