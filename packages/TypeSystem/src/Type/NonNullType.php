<?php

/**
 * This file is part of GraphQL TypeSystem package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Railt\TypeSystem\Type;

use GraphQL\Contracts\TypeSystem\Type\NonNullTypeInterface;

/**
 * {@inheritDoc}
 */
final class NonNullType extends WrappingType implements NonNullTypeInterface
{
    /**
     * {@inheritDoc}
     */
    public function __toString(): string
    {
        return \sprintf('%s!', (string)$this->getOfType());
    }
}
