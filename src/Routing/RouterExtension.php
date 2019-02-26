<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Routing;

use Railt\ClassLoader\ClassLoaderExtension;
use Railt\ClassLoader\ClassLoaderInterface;
use Railt\Container\Exception\ContainerInvocationException;
use Railt\Container\Exception\ContainerResolutionException;
use Railt\Foundation\Application;
use Railt\Foundation\Extension\Extension;
use Railt\Foundation\Extension\Status;
use Railt\GraphQL\CompilerInterface;
use Railt\Routing\Subscribers\ActionDispatcherSubscriber;
use Railt\Routing\Subscribers\DirectiveLoaderSubscriber;
use Railt\Routing\Subscribers\FieldResolveToActionSubscriber;

/**
 * Class RouterExtension
 */
class RouterExtension extends Extension
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'Routing';
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Provides the ability to create field resolvers using GraphQL SDL code.';
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
     * @return array
     */
    public function getDependencies(): array
    {
        return ['railt/railt' => ClassLoaderExtension::class];
    }

    /**
     * @throws ContainerInvocationException
     */
    public function register(): void
    {
        $this->registerIfNotRegistered(RouterInterface::class, function () {
            return new Router();
        });

        $directiveLoaderResolver = function (RouterInterface $router, ClassLoaderInterface $loader) {
            return new DirectiveLoader($this->app(), $router, $loader);
        };

        $this->registerIfNotRegistered(DirectiveLoader::class, $directiveLoaderResolver);
    }

    /**
     * @param CompilerInterface $compiler
     * @throws ContainerResolutionException
     */
    public function boot(CompilerInterface $compiler): void
    {
        $router = $this->make(RouterInterface::class);
        $loader = $this->make(DirectiveLoader::class);

        //
        // Subscribing to a field resolving event and trying to initialize the necessary directives.
        //
        $this->subscribe(new DirectiveLoaderSubscriber($loader));

        //
        // Subscribe to fields resolving, which creates a list of necessary arguments.
        //
        $this->subscribe(new FieldResolveToActionSubscriber($router, $this->events()));

        //
        // Subscribe to a method call event that should call the desired controller method.
        //
        $this->subscribe(new ActionDispatcherSubscriber($this->app()));
    }
}
