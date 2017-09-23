<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Reflection\Base;

use Railt\Reflection\Base\Containers\BaseFieldsContainer;
use Railt\Reflection\Contracts\Types\InterfaceType;
use Railt\Reflection\Contracts\Types\ObjectType;

/**
 * Class BaseObject
 */
abstract class BaseObject extends BaseNamedType implements ObjectType
{
    use BaseFieldsContainer;

    /**
     * @var array|InterfaceType[]
     */
    protected $interfaces = [];

    /**
     * @return iterable|InterfaceType[]
     */
    public function getInterfaces(): iterable
    {
        return \array_values($this->compiled()->interfaces);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasInterface(string $name): bool
    {
        return \array_key_exists($name, $this->compiled()->interfaces);
    }

    /**
     * @param string $name
     * @return null|InterfaceType
     */
    public function getInterface(string $name): ?InterfaceType
    {
        return $this->compiled()->interfaces[$name] ?? null;
    }

    /**
     * @return int
     */
    public function getNumberOfInterfaces(): int
    {
        return \count($this->compiled()->interfaces);
    }

    /**
     * @return string
     */
    public function getTypeName(): string
    {
        return 'Object';
    }
}
