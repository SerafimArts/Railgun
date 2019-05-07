<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Extension\ClassLoader;

use Phplrt\Io\File;
use Railt\SDL\Schema\CompilerInterface;
use Railt\Foundation\Application;
use Railt\Foundation\Application\CompilerExtension;
use Railt\Foundation\Extension\Extension;
use Railt\Foundation\Extension\Status;

/**
 * Class KernelExtension
 */
class ClassLoaderExtension extends Extension
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'ClassLoader';
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Provides the ability to reference PHP code from within GraphQL SDL files.';
    }

    /**
     * @return array
     */
    public function getDependencies(): array
    {
        return ['railt/railt' => CompilerExtension::class];
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return Application::VERSION;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return Status::STABLE;
    }

    /**
     * @return void
     */
    public function register(): void
    {
        $handler = function (CompilerInterface $compiler): ClassLoaderInterface {
            return new DirectiveClassLoader($compiler, $this->app);
        };

        $this->registerIfNotRegistered(ClassLoaderInterface::class, $handler);
    }
}
