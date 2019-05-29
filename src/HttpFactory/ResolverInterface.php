<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\HttpFactory;

use Railt\Http\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface ResolverInterface
 */
interface ResolverInterface
{
    /**
     * @param ServerRequestInterface $request
     * @return RequestInterface|null
     */
    public function resolve(ServerRequestInterface $request): ?RequestInterface;
}
