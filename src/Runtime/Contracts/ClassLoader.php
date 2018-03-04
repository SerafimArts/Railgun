<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Runtime\Contracts;

use Railt\Reflection\Contracts\Document;

/**
 * Interface ClassLoader
 */
interface ClassLoader
{
    /**
     * @param Document $document
     * @param string $class
     * @return string
     */
    public function load(Document $document, string $class): string;
}
