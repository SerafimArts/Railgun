<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Tests\Container;

use Railt\Container\Autowireable;
use Railt\Container\Container;
use Railt\Container\ContainerInterface;
use Psr\Container\ContainerInterface as PSRContainer;
use Railt\Container\Registrable;

/**
 * Class ContainerCompatibilityTestCase
 */
class ContainerCompatibilityTestCase extends AbstractContainerTestCase
{
    /**
     * @return void
     */
    public function testPSRCompatibility(): void
    {
        $this->assertInstanceOf(PSRContainer::class, new Container());
    }

    /**
     * @return void
     */
    public function testIsInterfaceCompatibility(): void
    {
        $this->assertInstanceOf(ContainerInterface::class, new Container());
    }

    /**
     * @return void
     */
    public function testIsAutowireable(): void
    {
        $this->assertInstanceOf(Autowireable::class, new Container());
    }

    /**
     * @return void
     */
    public function testIsRegistrable(): void
    {
        $this->assertInstanceOf(Registrable::class, new Container());
    }
}