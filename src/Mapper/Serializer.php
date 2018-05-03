<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Mapper;

use Railt\Container\ContainerInterface;
use Railt\Foundation\Kernel\Contracts\ClassLoader;
use Railt\Foundation\Kernel\Exceptions\InvalidActionException;
use Railt\Mapper\Exceptions\InvalidSignatureException;
use Railt\Reflection\Contracts\Behavior\AllowsTypeIndication;
use Railt\Reflection\Contracts\Definitions\InterfaceDefinition;
use Railt\Reflection\Contracts\Definitions\ObjectDefinition;
use Railt\Reflection\Contracts\Definitions\TypeDefinition;
use Railt\Reflection\Contracts\Definitions\UnionDefinition;
use Railt\Reflection\Contracts\Dependent\ArgumentDefinition;
use Railt\Reflection\Contracts\Dependent\FieldDefinition;
use Railt\Reflection\Contracts\Document;

/**
 * Class Serializer
 */
class Serializer
{
    /**
     * @var ClassLoader
     */
    private $loader;

    /**
     * - If the value is defined as a string, then this is an indication of type.
     * - If the value is defined as a ReflectionNamedType, then this is the indication of the class.
     * - If the value is NULL, then the argument can take anything.
     *
     * @var array|string[]|\ReflectionNamedType[]|null[]
     */
    private $signatures = [];

    /**
     * @var array|object[]
     */
    private $instances = [];

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Serializer constructor.
     * @param ClassLoader $loader
     * @param ContainerInterface $container
     */
    public function __construct(ClassLoader $loader, ContainerInterface $container)
    {
        $this->loader    = $loader;
        $this->container = $container;
    }

    /**
     * @param FieldDefinition $field
     * @param Document $document
     * @param string $action
     * @param mixed $result
     * @return iterable
     * @throws \Railt\Mapper\Exceptions\InvalidSignatureException
     * @throws \Railt\Foundation\Kernel\Exceptions\InvalidActionException
     */
    public function serialize(FieldDefinition $field, Document $document, string $action, $result)
    {
        [$class, $method] = $this->loader->action($document, $action);

        $requiredType = $this->getSignature($class, $method);

        return $this->resolveMap($field, $requiredType, $result, $class, $method);
    }

    /**
     * @param ArgumentDefinition $type
     * @param Document $document
     * @param string $action
     * @param $value
     * @return mixed
     * @throws \Railt\Mapper\Exceptions\InvalidSignatureException
     * @throws \Railt\Foundation\Kernel\Exceptions\InvalidActionException
     */
    public function unserialize(ArgumentDefinition $type, Document $document, string $action, $value)
    {
        [$class, $method] = $this->loader->action($document, $action);

        $requiredType = $this->getSignature($class, $method);

        return $this->resolveMap($type, $requiredType, $value, $class, $method);
    }

    /**
     * @param AllowsTypeIndication $type
     * @param null|string|\ReflectionNamedType $requiredType
     * @param iterable|array|mixed $result
     * @param string $class
     * @param string $method
     * @return array|mixed
     */
    private function resolveMap(AllowsTypeIndication $type, $requiredType, $result, string $class, string $method)
    {
        $result = $this->map($class, $method, $requiredType, $result);

        if ($result instanceof \Traversable) {
            $result = \iterator_to_array($result);
        }

        if (! $this->isPolymorphic($type) && $this->shouldProvideTypeName($result)) {
            $result['__typename'] = $type->getTypeDefinition()->getName();
        }

        return $result;
    }

    /**
     * @param mixed $result
     * @return bool
     */
    private function shouldProvideTypeName($result): bool
    {
        return \is_array($result) && ! \array_key_exists('__typename', $result);
    }

    /**
     * @param AllowsTypeIndication $field
     * @return bool
     */
    private function isPolymorphic(AllowsTypeIndication $field): bool
    {
        $type = $field->getTypeDefinition();

        return $type instanceof UnionDefinition || $type instanceof ObjectDefinition;
    }

    /**
     * @param string $class
     * @return mixed|object
     */
    private function instance(string $class)
    {
        if (\array_key_exists($class, $this->instances)) {
            return $this->instances[$class];
        }

        return $this->instances[$class] = $this->container->make($class);
    }

    /**
     * @param string $class
     * @param string $method
     * @param string|null|\ReflectionNamedType $requiredType
     * @param mixed $result
     * @return mixed
     */
    private function map(string $class, string $method, $requiredType, $result)
    {
        if ($this->matchType($requiredType, $result)) {
            return $this->instance($class)->$method($result);
        }

        return $result;
    }

    /**
     * @param string|null|\ReflectionNamedType $requiredType
     * @param $value
     * @return bool
     */
    private function matchType($requiredType, $value): bool
    {
        if ($requiredType === null) {
            return true;
        }

        if (\is_string($requiredType) && \strtolower(\gettype($value)) === $requiredType) {
            return true;
        }

        if ($requiredType instanceof \ReflectionNamedType) {
            $name = $requiredType->getName();

            return $requiredType->isBuiltin()
                ? $this->matchTypeHint($name, $value)
                : $value instanceof $name;
        }

        return false;
    }

    /**
     * @param string $hint
     * @param mixed $value
     * @return bool
     */
    private function matchTypeHint(string $hint, $value): bool
    {
        $matcher = '\\is_' . $hint;

        if (\function_exists($matcher)) {
            return $matcher($value);
        }

        return \mb_strtolower(\gettype($value)) === $hint;
    }

    /**
     * @param string $class
     * @param string $method
     * @return string|null|\ReflectionClass
     * @throws \Railt\Mapper\Exceptions\InvalidSignatureException
     * @throws \Railt\Foundation\Kernel\Exceptions\InvalidActionException
     */
    private function getSignature(string $class, string $method)
    {
        $key = $class . '@' . $method;

        if (! \array_key_exists($key, $this->signatures)) {
            $parameters = $this->extractMethod($class, $method)->getParameters();

            if (\count($parameters) !== 1) {
                $error = 'For an action "%s@%s", only one argument is required, but %d given';
                throw new InvalidSignatureException(\sprintf($error, $class, $method, \count($parameters)));
            }

            /** @var \ReflectionParameter $parameter */
            $parameter = \reset($parameters);

            $this->signatures[$key] = $parameter->getType() ?? $parameter->getClass();
        }

        return $this->signatures[$key];
    }

    /**
     * @param string $class
     * @param string $method
     * @return \ReflectionMethod
     * @throws \Railt\Foundation\Kernel\Exceptions\InvalidActionException
     */
    private function extractMethod(string $class, string $method): \ReflectionMethod
    {
        try {
            $reflection = new \ReflectionClass($class);

            if ($reflection->hasMethod($method)) {
                return $reflection->getMethod($method);
            }

            $error = 'In class "%s" there is no required method "%s"';
            throw new InvalidActionException(\sprintf($error, $class, $method));
        } catch (\ReflectionException $e) {
            $error = 'Error while extracting the action "%s@%s": %s';
            throw new InvalidActionException(\sprintf($error, $class, $method, $e->getMessage()));
        }
    }
}
