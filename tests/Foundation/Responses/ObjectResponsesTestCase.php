<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Tests\Foundation;

use Railt\Component\Http\Request;
use Railt\Foundation\ConnectionInterface;
use Railt\Foundation\Event\Resolver\FieldResolve;
use Railt\Tests\Foundation\Responses\ResponsesTestCase;
use Railt\Tests\Foundation\Stub\TraversableObject;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ObjectResponsesTestCase
 */
class ObjectResponsesTestCase extends ResponsesTestCase
{
    /**
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testNullableDefaultResponse(): void
    {
        $response = $this->connection()->request(new Request('{ nullable { a } }'));

        $this->assertSame(['nullable' => null], $response->getData());
        $this->assertSame([], $response->getErrors());
    }

    /**
     * @param \Closure|null $resolver
     * @return ConnectionInterface
     * @throws \InvalidArgumentException
     * @throws \LogicException
     */
    public function connection(\Closure $resolver = null): ConnectionInterface
    {
        return parent::connection(function (EventDispatcherInterface $events) use ($resolver): void {
            if ($resolver) {
                $events->addListener(FieldResolve::class, function (FieldResolve $e) use ($resolver): void {
                    $resolver($e);
                });
            }
        });
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testNullableResponse(): void
    {
        $response = $this->request('nullable', '{ a }', function () {
            return ['a' => '42'];
        });

        $this->assertSame(['nullable' => ['a' => '42']], $response->getData());
        $this->assertSame([], $response->getErrors());
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testObjectResponseWithPublicFields(): void
    {
        $response = $this->request('nullable', '{ a, b }', function () {
            return new class() {
                public $a = 42;
            };
        });

        $this->assertSame(['nullable' => ['a' => '42', 'b' => null]], $response->getData());
        $this->assertSame([], $response->getErrors());
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testObjectResponseIsTraversable(): void
    {
        $response = $this->request('nullable', '{ a, b }', function () {
            return new TraversableObject(['b' => 100500]);
        });

        $this->assertSame(['nullable' => ['a' => null, 'b' => '100500']], $response->getData());
        $this->assertSame([], $response->getErrors());
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testListResponse(): void
    {
        $response = $this->request('list', '{ a, b }', function () {
            return [
                ['a' => 1, 'b' => 1],
                ['a' => 2, 'b' => 2],
                ['a' => 3],
                ['b' => 4],
            ];
        });

        $this->assertSame([
            'list' => [
                ['a' => '1', 'b' => '1'],
                ['a' => '2', 'b' => '2'],
                ['a' => '3', 'b' => null],
                ['a' => null, 'b' => '4'],
            ],
        ], $response->getData());

        $this->assertSame([], $response->getErrors());
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testIterableResponse(): void
    {
        $response = $this->request('list', '{ a, b }', function () {
            return new \ArrayObject([
                ['a' => '1', 'b' => '1'],
                ['a' => '2', 'b' => null],
                ['a' => null, 'b' => '3'],
            ]);
        });

        $this->assertSame(['list' => [
            ['a' => '1', 'b' => '1'],
            ['a' => '2', 'b' => null],
            ['a' => null, 'b' => '3'],
        ]], $response->getData());
        $this->assertSame([], $response->getErrors());
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testIterableOfIterablesResponse(): void
    {
        $response = $this->request('list', '{ a, b }', function () {
            return new \ArrayObject([
                new \ArrayObject(['a' => '1', 'b' => '1']),
                new \ArrayObject(['a' => '2', 'b' => null]),
                new \ArrayObject(['a' => null, 'b' => '3']),
            ]);
        });

        $this->assertSame(['list' => [
            ['a' => '1', 'b' => '1'],
            ['a' => '2', 'b' => null],
            ['a' => null, 'b' => '3'],
        ]], $response->getData());
        $this->assertSame([], $response->getErrors());
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testObjectAsJsonString(): void
    {
        $response = $this->request('a', '', function () {
            return new \ArrayObject(['example' => 42]);
        });

        $this->assertSame(['a' => '{"example":42}'], $response->getData());
        $this->assertSame([], $response->getErrors());
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testStringableObjectAsString(): void
    {
        $response = $this->request('a', '', function () {
            return new class() {
                public function __toString()
                {
                    return 'example value';
                }
            };
        });

        $this->assertSame(['a' => 'example value'], $response->getData());
        $this->assertSame([], $response->getErrors());
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testObjectAsInt(): void
    {
        $response = $this->request('c', '', function () {
            return new \ArrayObject(['example' => 42]);
        });

        $this->assertSame(['c' => null], $response->getData());
        $this->assertGreaterThan(0, \count($response->getErrors()));
    }

    /**
     * @return string
     */
    protected function getSchema(): string
    {
        return '
            schema {
                query: Query
            }
            
            type Query {
                nullable: Query
                non_null: Query!
                list: [Query]
                list_of_non_nulls: [Query!]
                non_null_list: [Query]!
                non_null_list_of_non_nulls: [Query!]!
                
                ## VALUES
                a: String, b: String, c: Int
            }
        ';
    }
}
