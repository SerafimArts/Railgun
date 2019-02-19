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
use Railt\Foundation\Application\ProvidesConsoleApplication;
use Railt\Foundation\Application\ProvidesEnvironment;
use Railt\Foundation\Application\ProvidesExtensions;
use Railt\Io\Readable;

/**
 * Interface ApplicationInterface
 */
interface ApplicationInterface extends ContainerInterface, ProvidesConsoleApplication, ProvidesExtensions, ProvidesEnvironment
{
    /**
     * @param Readable $schema
     * @return ConnectionInterface
     */
    public function connect(Readable $schema): ConnectionInterface;
}
