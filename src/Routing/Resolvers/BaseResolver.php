<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Routing\Resolvers;

use Railt\Foundation\Events\ActionDispatched;
use Railt\Foundation\Events\ActionDispatching;
use Railt\Http\InputInterface;
use Railt\Reflection\Contracts\Definitions\EnumDefinition;
use Railt\Reflection\Contracts\Definitions\ScalarDefinition;
use Railt\Routing\Store\ObjectBox;
use Railt\Routing\Store\Store;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class BaseResolver
 */
abstract class BaseResolver implements Resolver
{
    /**
     * @var EventDispatcherInterface
     */
    private $events;

    /**
     * @var Store
     */
    protected $store;

    /**
     * BaseResolver constructor.
     * @param EventDispatcherInterface $events
     * @param Store $store
     */
    public function __construct(EventDispatcherInterface $events, Store $store)
    {
        $this->events = $events;
        $this->store  = $store;
    }

    /**
     * @param InputInterface $input
     * @param null|ObjectBox $parent
     */
    abstract protected function withParent(InputInterface $input, ?ObjectBox $parent): void;

    /**
     * @param InputInterface $input
     * @param mixed $response
     * @return mixed
     * @throws \RuntimeException
     */
    protected function response(InputInterface $input, $response)
    {
        $field = $input->getFieldDefinition();

        if ($field->isList()) {
            if (\is_iterable($response)) {
                return $this->formatList($input, $response);
            }

            if ($response === null) {
                return null;
            }

            $error = 'Return type of %s list should be an iterable, but %s returns';
            throw new \RuntimeException(\sprintf($error, $input->getFieldName(), \strtolower(\gettype($response))));
        }

        return $this->format($input, $response);
    }

    /**
     * @param InputInterface $input
     * @param iterable $response
     * @return array
     */
    private function formatList(InputInterface $input, iterable $response): array
    {
        $result = [];

        foreach ($response as $item) {
            $result[] = $this->format($input, $item);
        }

        return $result;
    }

    /**
     * @param InputInterface $input
     * @param mixed $response
     * @return mixed
     */
    private function format(InputInterface $input, $response)
    {
        $formatted = $this->dispatched($input, $response);

        if ($formatted === null) {
            return null;
        }

        if ($this->onScalar($input)) {
            return $formatted;
        }

        $object = new ObjectBox($response, $formatted);

        return $this->store->set($input, $object);
    }

    /**
     * @param InputInterface $input
     * @return bool
     */
    private function onScalar(InputInterface $input): bool
    {
        $type = $input->getFieldDefinition()->getTypeDefinition();

        return $type instanceof ScalarDefinition || $type instanceof EnumDefinition;
    }

    /**
     * @param InputInterface $input
     * @return array
     */
    protected function getParameters(InputInterface $input): array
    {
        return $this->dispatching($input, []);
    }

    /**
     * @param InputInterface $input
     * @param array $parameters
     * @return array
     */
    private function dispatching(InputInterface $input, array $parameters): array
    {
        $event = new ActionDispatching($input, $parameters);

        $this->events->dispatch(ActionDispatching::class, $event);

        return $event->getParameters();
    }

    /**
     * @param InputInterface $input
     * @param mixed $response
     * @return mixed
     */
    private function dispatched(InputInterface $input, $response)
    {
        $event = new ActionDispatched($input, $response);

        $this->events->dispatch(ActionDispatched::class, $event);

        return $event->getResponse();
    }
}
