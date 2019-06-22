<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Contracts\Http\Response;

use Railt\Contracts\Exception\GraphQLExceptionInterface;

/**
 * Interface ExceptionsProviderInterface
 */
interface ExceptionsProviderInterface
{
    /**
     * @return array|GraphQLExceptionInterface[]
     */
    public function getExceptions(): array;
}
