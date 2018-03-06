<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Routing;

/**
 * Class Store
 */
class Store
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @param string $key
     * @param mixed $data
     * @return array|iterable|mixed
     */
    public function set(string $key, $data)
    {
        $this->data[$key] = $data;

        return $data;
    }

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed|null
     */
    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return \array_key_exists($key, $this->data);
    }
}
