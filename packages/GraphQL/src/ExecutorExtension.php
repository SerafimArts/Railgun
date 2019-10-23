<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\GraphQL;

use Railt\Foundation\Extension\Status;
use Railt\Foundation\Extension\Extension;
use Railt\Contracts\GraphQL\FactoryInterface;

/**
 * Class ExecutorExtension
 */
class ExecutorExtension extends Extension
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->register(FactoryInterface::class, fn (): FactoryInterface => new Factory());
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'GraphQL Executor';
    }

    /**
     * {@inheritDoc}
     */
    public function getStatus(): string
    {
        return Status::STABLE;
    }

    /**
     * {@inheritDoc}
     */
    public function getVersion(): string
    {
        return $this->app->getVersion();
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription(): string
    {
        return 'Registers the GraphQL query language parser and executor';
    }
}
