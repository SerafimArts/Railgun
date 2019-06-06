<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Http\Exception;

use Phplrt\Exception\MutableException\MutableFileInterface;
use Phplrt\Exception\MutableException\MutablePositionInterface;
use Railt\Http\Exception\Location\MutableLocationsProviderInterface;
use Railt\Http\Exception\Path\MutablePathProviderInterface;
use Railt\Http\Extension\MutableExtensionProviderInterface;

/**
 * Interface MutableGraphQLExceptionInterface
 */
interface MutableGraphQLExceptionInterface extends
    GraphQLExceptionInterface,
    MutablePathProviderInterface,
    MutableLocationsProviderInterface,
    MutableFileInterface,
    MutablePositionInterface,
    MutableExtensionProviderInterface
{
    /**
     * @return MutableGraphQLExceptionInterface|$this
     */
    public function publish(): self;

    /**
     * @return MutableGraphQLExceptionInterface|$this
     */
    public function hide(): self;
}
