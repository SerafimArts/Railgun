<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Foundation;

use Railt\Container\ContainerInterface;
use Phplrt\Io\Readable;
use Railt\Foundation\Application\ProvidesConsoleApplication;
use Railt\Foundation\Application\ProvidesExtensions;

/**
 * Interface ApplicationInterface
 */
interface ApplicationInterface extends ContainerInterface, ProvidesConsoleApplication, ProvidesExtensions
{
    /**
     * @param Readable $schema
     * @return ConnectionInterface
     */
    public function connect(Readable $schema): ConnectionInterface;
}
