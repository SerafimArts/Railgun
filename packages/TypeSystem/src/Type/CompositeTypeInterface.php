<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\TypeSystem\Type;

/**
 * These types may describe the parent context of a selection set.
 *
 * <code>
 *  export type GraphQLCompositeType =
 *      | GraphQLObjectType
 *      | GraphQLInterfaceType
 *      | GraphQLUnionType
 *  ;
 * <code>
 */
interface CompositeTypeInterface
{
}
