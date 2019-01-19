<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Foundation\Webonyx;

use Railt\Foundation\Application;
use Railt\Foundation\Event\EventsExtension;
use Railt\Foundation\Extension\Extension;
use Railt\Foundation\Extension\Status;
use Railt\Foundation\Webonyx\Subscribers\ConnectionSubscriber;
use Railt\Foundation\Webonyx\Subscribers\RequestsSubscriber;
use Railt\Foundation\Webonyx\Subscribers\TypeResolvingFixPathSubscriber;

/**
 * Class WebonyxExtension
 */
class WebonyxExtension extends Extension
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'Webonyx';
    }

    /**
     * @return array
     */
    public function getDependencies(): array
    {
        return ['railt/railt' => EventsExtension::class];
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
     * @return string
     */
    public function getDescription(): string
    {
        return 'Webonyx GraphQL reference implementation extension';
    }

    /**
     * @param bool $debug
     * @return void
     */
    public function boot(bool $debug = false): void
    {
        //
        // Listen all connections.
        //
        $this->subscribe($connections = new ConnectionSubscriber($this->events(), $debug));

        //
        // Listen requests and delegate it to several connection.
        //
        $this->subscribe($requests = new RequestsSubscriber($debug, $connections));

        //
        // Fix of https://github.com/webonyx/graphql-php/issues/396
        // Reproduced to Webonyx version < 0.12.6 (including)
        //
        $this->subscribe(new TypeResolvingFixPathSubscriber($connections));
    }
}
