<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Normalization;

use Railt\Json\Json;

/**
 * Class ObjectNormalizer
 */
class ObjectNormalizer extends Normalizer
{
    /**
     * @param mixed $result
     * @param int $options
     * @return array|bool|float|int|mixed|string
     * @throws \Railt\Json\Exception\JsonException
     */
    public function normalize($result, int $options = 0)
    {
        if (\is_object($result)) {
            return ($options & static::TYPE_SCALAR)
                ? $this->renderScalar($result)
                : $this->renderObject($result);
        }

        return $result;
    }

    /**
     * @param mixed $result
     * @return mixed
     */
    private function renderScalar($result)
    {
        if (\method_exists($result, '__toString')) {
            return (string)$result;
        }

        if (\property_exists($result, 'value')) {
            return $result->value;
        }

        return $result;
    }

    /**
     * @param mixed $result
     * @return mixed
     * @throws \Railt\Json\Exception\JsonException
     */
    private function renderObject($result)
    {
        return Json::decoder()
            ->withOptions(\JSON_OBJECT_AS_ARRAY)
            ->decode(Json::encode($result));
    }
}
