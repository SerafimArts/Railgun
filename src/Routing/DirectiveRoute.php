<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Routing;

use Illuminate\Support\Str;
use Railt\Container\ContainerInterface;
use Railt\Reflection\Contracts\Dependent\FieldDefinition;
use Railt\Reflection\Contracts\Document;
use Railt\Reflection\Contracts\Invocations\DirectiveInvocation;
use Railt\Reflection\Contracts\Invocations\InputInvocation;
use Railt\Routing\Exceptions\InvalidActionException;

/**
 * Class DirectiveRoute
 */
class DirectiveRoute extends Route
{
    /**
     * DirectiveRoute constructor.
     * @param ContainerInterface $container
     * @param FieldDefinition $field
     * @param DirectiveInvocation $directive
     * @throws InvalidActionException
     */
    public function __construct(ContainerInterface $container, FieldDefinition $field, DirectiveInvocation $directive)
    {
        parent::__construct($container, $field);

        //
        // @route( action: "Controller@action" )
        //
        $this->exportAction($directive->getDocument(), $directive->getPassedArgument('action'));

        //
        // @route( relation: {parent: "key", child: "key"} )
        //
        $relation = $directive->getPassedArgument('relation');

        if ($relation) {
            $this->exportRelation($relation);
        }

        //
        // @route( operations: [OperationName] )
        //
        $this->exportOperations($directive);
    }

    /**
     * @param Document $document
     * @param string $urn
     * @throws \Railt\Routing\Exceptions\InvalidActionException
     */
    private function exportAction(Document $document, string $urn): void
    {
        [$controller, $action] = \tap(\explode('@', $urn), function (array $parts) use ($urn): void {
            if (\count($parts) !== 2) {
                $error = 'The action route argument must contain an urn in the format "Class@action", but "%s" given';
                throw new InvalidActionException(\sprintf($error, $urn));
            }
        });

        $class = $this->loadControllerClass($document, $controller);

        $instance = $this->container->make($class);

        $this->then(\Closure::fromCallable([$instance, $action]));
    }

    /**
     * @param Document $document
     * @param string $controller
     * @return string
     * @throws \Railt\Routing\Exceptions\InvalidActionException
     */
    private function loadControllerClass(Document $document, string $controller): string
    {
        if (\class_exists($controller)) {
            return $controller;
        }

        foreach ($document->getDirectives('use') as $directive) {
            $class = $directive->getPassedArgument('class');
            $alias = $directive->getPassedArgument('as');

            if ($alias === $controller) {
                return $class;
            }

            if (Str::endsWith($class, '\\' . $alias) && \class_exists($class)) {
                return $class;
            }
        }

        $error = 'Class "%s" is not found in the definition of route action argument';
        throw new InvalidActionException(\sprintf($error, $controller));
    }

    /**
     * @param InputInvocation $relation
     */
    private function exportRelation(InputInvocation $relation): void
    {
        $parent = $relation->getPassedArgument('parent');
        $child  = $relation->getPassedArgument('child');

        $this->relation($parent, $child);
    }

    /**
     * @param DirectiveInvocation $directive
     */
    private function exportOperations(DirectiveInvocation $directive): void
    {
        switch ($directive->getName()) {
            case 'query':
                $this->on('query');
                break;
            case 'mutation':
                $this->on('mutation');
                break;
            case 'subscription':
                $this->on('subscription');
                break;
        }
    }
}
