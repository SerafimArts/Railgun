<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Reflection\Base;

use Railt\Reflection\Base\Behavior\BaseChild;
use Railt\Reflection\Base\Behavior\BaseTypeIndicator;
use Railt\Reflection\Contracts\Types\ArgumentType;

/**
 * Class BaseArgument
 */
abstract class BaseArgument extends BaseNamedType implements ArgumentType
{
    use BaseChild;
    use BaseTypeIndicator;

    /**
     * @var mixed
     */
    protected $defaultValue;

    /**
     * @var bool
     */
    protected $hasDefaultValue = false;

    /**
     * @return string
     */
    public function getTypeName(): string
    {
        return 'Argument';
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        if ($this->compiled()->hasDefaultValue) {
            return $this->defaultValue;
        }

        return null;
    }

    /**
     * @return bool
     */
    public function hasDefaultValue(): bool
    {
        return $this->compiled()->defaultValue !== null || $this->hasDefaultValue;
    }
}
