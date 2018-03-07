<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Routing;

use Railt\Http\InputInterface;
use Railt\Reflection\Contracts\Definitions\ObjectDefinition;
use Railt\Reflection\Contracts\Definitions\TypeDefinition;
use Railt\Routing\Contracts\RouterInterface;

/**
 * Class FieldResolver
 */
class FieldResolver
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ActionResolver
     */
    private $resolver;

    /**
     * FieldResolver constructor.
     * @param RouterInterface $router
     * @param ActionResolver $resolver
     */
    public function __construct(RouterInterface $router, ActionResolver $resolver)
    {
        $this->router   = $router;
        $this->resolver = $resolver;
    }

    /**
     * @param $parent
     * @param InputInterface $input
     * @return array|mixed
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function handle($parent, InputInterface $input)
    {
        $field = $input->getFieldDefinition();

        foreach ($this->router->get($field) as $route) {
            if (! $route->matchOperation($input->getOperation())) {
                continue;
            }

            $parameters = \array_merge($input->all(), [
                InputInterface::class => $input,
                TypeDefinition::class => $field,
            ]);

            return $this->resolver->call($route, $input, $parameters, $parent);
        }

        if ($parent === null && $field->getTypeDefinition() instanceof ObjectDefinition && $field->isNonNull()) {
            return [];
        }

        return $parent[$field->getName()] ?? null;
    }
}
