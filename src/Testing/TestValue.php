<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Testing;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Constraint\Constraint;
use Railt\Testing\Common\DataSelection;
use Railt\Testing\Common\MethodsAccess;

/**
 * Class TestValue
 *
 * @property TestResponse $and
 * @property TestResponse $exists
 * @property TestResponse $notExists
 * @property TestResponse $empty
 * @property TestResponse $notEmpty
 * @property TestResponse $true
 * @property TestResponse $notTrue
 * @property TestResponse $false
 * @property TestResponse $notFalse
 * @property TestResponse $null
 * @property TestResponse $notNull
 * @property TestResponse $finite
 * @property TestResponse $infinite
 * @property TestResponse $nan
 * @property TestResponse $json
 *
 * @property TestValue $dump
 * @property void $dd
 */
class TestValue
{
    use MethodsAccess;
    use DataSelection;

    /**
     * @var string
     */
    protected $field;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var TestResponse
     */
    protected $response;

    /**
     * @var bool
     */
    protected $isExists;

    /**
     * TestValue constructor.
     * @param string $field
     * @param mixed $value
     * @param TestResponse $response
     * @param bool $exists
     */
    public function __construct(string $field, $value, TestResponse $response, bool $exists = false)
    {
        $this->field    = $field;
        $this->value    = $value;
        $this->response = $response;
        $this->isExists = $exists;
    }

    /**
     * @return TestResponse
     */
    public function and(): TestResponse
    {
        return $this->response;
    }

    /**
     * @param string $field
     * @return TestValue
     */
    public function where(string $field): self
    {
        return $this->response->where($field);
    }

    /**
     * @param string $name
     * @return TestValue
     */
    public function field(string $name): self
    {
        return $this->andWhere($name);
    }

    /**
     * @param string $name
     * @return TestValue
     */
    public function andWhere(string $name): self
    {
        [$result, $exists] = $this->get($name, $this->value);

        return new self($name, $result, $this->response, $exists);
    }

    /**
     * @param string $method
     * @return string
     */
    private function message(string $method): string
    {
        return \vsprintf('%s assertion of %sfield "%s" with value %s are failed%s', [
            '->' . $method . '(...)',
            $this->isExists ? 'non-existent ' : '',
            $this->field,
            \json_encode($this->value),
            $this->response->exceptionsMessage(),
        ]);
    }

    /**
     * @return TestValue
     */
    public function dump(): TestValue
    {
        \dump($this->value);

        return $this;
    }

    /**
     * @return void
     */
    public function dd(): void
    {
        $this->dump();
        die(-1);
    }

    /**
     * @return TestValue
     */
    public function exists(): self
    {
        Assert::assertTrue($this->isExists, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @return TestValue
     * @throws \PHPUnit\Framework\AssertionFailedError
     */
    public function notExists(): self
    {
        Assert::assertFalse($this->isExists, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @param int|string $field
     * @return TestValue
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function hasField($field): self
    {
        Assert::assertArrayHasKey($field, (array)$this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @param int[]|string[] ...$fields
     * @return TestValue
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function hasFields(...$fields): self
    {
        foreach ($fields as $field) {
            $this->hasField($field);
        }

        return $this;
    }

    /**
     * @param array|\ArrayAccess $subset
     * @param bool $strict
     * @return TestValue
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function subset($subset, bool $strict = false): self
    {
        Assert::assertArraySubset($subset, (array)$this->value, $strict, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @param int|string $field
     * @return TestValue
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function notHasField($field): self
    {
        Assert::assertArrayNotHasKey($field, (array)$this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @param int[]|string[] $fields
     * @return TestValue
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function notHasFields(...$fields): self
    {
        foreach ($fields as $field) {
            $this->notHasField($field);
        }

        return $this;
    }

    /**
     * @param mixed $needle
     * @param bool $ignoreCase
     * @return TestValue
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function contains($needle, bool $ignoreCase = false): self
    {
        Assert::assertContains($needle, $this->value, $this->message(__FUNCTION__), $ignoreCase);

        return $this;
    }

    /**
     * @param mixed $needle
     * @param bool $ignoreCase
     * @return TestValue
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function notContains($needle, bool $ignoreCase = false): self
    {
        Assert::assertNotContains($needle, $this->value, $this->message(__FUNCTION__), $ignoreCase);

        return $this;
    }

    /**
     * @param string $type
     * @param bool|null $nativeType
     * @return TestValue
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function containsOnly(string $type, bool $nativeType = null): self
    {
        Assert::assertContainsOnly($type, $this->value, $nativeType, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @param string $type
     * @param bool|null $nativeType
     * @return TestValue
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function notContainsOnly(string $type, bool $nativeType = null): self
    {
        Assert::assertNotContainsOnly($type, $this->value, $nativeType, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @param int $expectedCount
     * @return TestValue
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function count(int $expectedCount): self
    {
        Assert::assertCount($expectedCount, (array)$this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @param int $expectedCount
     * @return TestValue
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function notCount(int $expectedCount): self
    {
        Assert::assertNotCount($expectedCount, $this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @param mixed $value
     * @return TestValue
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function equals($value): self
    {
        Assert::assertEquals($value, $this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @param mixed $value
     * @return TestValue
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function notEquals($value): self
    {
        Assert::assertNotEquals($value, $this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @return TestValue
     * @throws \PHPUnit\Framework\AssertionFailedError
     */
    public function empty(): self
    {
        Assert::assertEmpty($this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @return TestValue
     * @throws \PHPUnit\Framework\AssertionFailedError
     */
    public function notEmpty(): self
    {
        Assert::assertNotEmpty($this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @param mixed $value
     * @return TestValue
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function greaterThan($value): self
    {
        Assert::assertGreaterThan($value, $this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @param mixed $value
     * @return TestValue
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \PHPUnit\Framework\Exception
     */
    public function greaterThanOrEqual($value): self
    {
        Assert::assertGreaterThanOrEqual($value, $this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @param mixed $value
     * @return TestValue
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function lessThan($value): self
    {
        Assert::assertLessThan($value, $this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @param mixed $value
     * @return TestValue
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \PHPUnit\Framework\Exception
     */
    public function lessThanOrEqual($value): self
    {
        Assert::assertLessThanOrEqual($value, $this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @return TestValue
     * @throws \PHPUnit\Framework\AssertionFailedError
     */
    public function true(): self
    {
        Assert::assertTrue($this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @return TestValue
     * @throws \PHPUnit\Framework\AssertionFailedError
     */
    public function notTrue(): self
    {
        Assert::assertNotTrue($this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @return TestValue
     * @throws \PHPUnit\Framework\AssertionFailedError
     */
    public function false(): self
    {
        Assert::assertFalse($this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @return TestValue
     * @throws \PHPUnit\Framework\AssertionFailedError
     */
    public function notFalse(): self
    {
        Assert::assertNotFalse($this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @return TestValue
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function null(): self
    {
        Assert::assertNull($this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @return TestValue
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function notNull(): self
    {
        Assert::assertNotNull($this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @return TestValue
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function finite(): self
    {
        Assert::assertFinite($this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @return TestValue
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function infinite(): self
    {
        Assert::assertInfinite($this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @return TestValue
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function nan(): self
    {
        Assert::assertNan($this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @param mixed $value
     * @return TestValue
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function same($value): self
    {
        Assert::assertSame($value, $this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @param mixed $value
     * @return TestValue
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function notSame($value): self
    {
        Assert::assertNotSame($value, $this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @param mixed $value
     * @return TestValue
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function typeOf($value): self
    {
        Assert::assertInternalType($value, $this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @param mixed $value
     * @return TestValue
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function notTypeOf($value): self
    {
        Assert::assertNotInternalType($value, $this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @param mixed $pattern
     * @return TestValue
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function matches(string $pattern): self
    {
        Assert::assertRegExp($pattern, $this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @param mixed $pattern
     * @return TestValue
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function notMatches(string $pattern): self
    {
        Assert::assertNotRegExp($pattern, $this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @param iterable $expected
     * @return TestValue
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function sameSize(iterable $expected): self
    {
        Assert::assertSameSize($expected, $this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @param iterable $expected
     * @return TestValue
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function notSameSize(iterable $expected): self
    {
        Assert::assertNotSameSize($expected, $this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @param string $format
     * @return TestValue
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function matchesFormat(string $format): self
    {
        Assert::assertStringMatchesFormat($format, $this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @param string $format
     * @return TestValue
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function notMatchesFormat(string $format): self
    {
        Assert::assertStringNotMatchesFormat($format, $this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @param string $prefix
     * @return TestValue
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function startsWith(string $prefix): self
    {
        Assert::assertStringStartsWith($prefix, $this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @param string $prefix
     * @return TestValue
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function startsNotWith(string $prefix): self
    {
        Assert::assertStringStartsNotWith($prefix, $this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @param string $suffix
     * @return TestValue
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function endsWith(string $suffix): self
    {
        Assert::assertStringEndsWith($suffix, $this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @param string $suffix
     * @return TestValue
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function endsNotWith(string $suffix): self
    {
        Assert::assertStringEndsNotWith($suffix, $this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @param Constraint $constraint
     * @return TestValue
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function assert(Constraint $constraint): self
    {
        Assert::assertThat($this->value, $constraint, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @return TestValue
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function json(): self
    {
        Assert::assertJson($this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @param string $json
     * @return TestValue
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function equalsJson(string $json): self
    {
        Assert::assertJsonStringEqualsJsonString($json, $this->value, $this->message(__FUNCTION__));

        return $this;
    }

    /**
     * @param string $json
     * @return TestValue
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function notEqualsJson(string $json): self
    {
        Assert::assertJsonStringNotEqualsJsonString($json, $this->value, $this->message(__FUNCTION__));

        return $this;
    }
}
