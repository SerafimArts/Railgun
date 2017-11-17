<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Tests\Compiler;

use Monolog\Logger;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;
use Railt\Compiler\Compiler;
use League\Flysystem\Filesystem;
use Railt\Tests\AbstractTestCase;
use Monolog\Handler\StreamHandler;
use Cache\Adapter\Common\CacheItem;
use League\Flysystem\Adapter\Local;
use Symfony\Component\Finder\Finder;
use Railt\Reflection\Filesystem\File;
use League\Flysystem\Plugin\ListFiles;
use Railt\Compiler\Persisting\Persister;
use Railt\Reflection\Contracts\Document;
use Cache\Adapter\Common\AbstractCachePool;
use Railt\Compiler\Persisting\Psr6Persister;
use Railt\Compiler\Persisting\ArrayPersister;
use Railt\Compiler\Persisting\Psr16Persister;
use Railt\Compiler\Persisting\NullablePersister;
use Cache\Adapter\Filesystem\FilesystemCachePool;
use Railt\Compiler\Persisting\EmulatingPersister;
use Railt\Reflection\Filesystem\ReadableInterface;

/**
 * Class AbstractReflectionTestCase.
 */
abstract class AbstractCompilerTestCase extends AbstractTestCase
{
    /**
     * @var string
     */
    protected $resourcesPath = '';

    /**
     * @var string
     */
    protected $specDirectory = __DIR__ . '/.resources';

    /**
     * @var bool
     */
    private static $booted = false;

    /**
     * @return void
     * @throws \League\Flysystem\FileNotFoundException
     * @throws \LogicException
     */
    public function setUp(): void
    {
        if (self::$booted === false) {
            self::$booted = true;

            $filesystem = new Filesystem(new Local(__DIR__ . '/.temp/'));
            $filesystem->addPlugin(new ListFiles());

            foreach ($filesystem->listFiles('/', true) as $file) {
                if (Str::startsWith($file['basename'], '.')) {
                    continue;
                }
                // Clear cache
                $filesystem->delete($file['path']);
            }
        }

        parent::setUp();
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\SkippedTestError
     */
    public function testProviderIsLoadable(): void
    {
        if (! \method_exists($this, 'provider')) {
            static::markTestSkipped(__CLASS__ . ' does not provide a data provider');

            return;
        }

        static::assertInternalType('array', $this->provider());
        foreach ($this->provider() ?? [] as $item) {
            static::assertInternalType('array', $item);
        }
    }

    /**
     * @param string $body
     * @return array
     * @throws \League\Flysystem\FileNotFoundException
     * @throws \LogicException
     */
    protected function dataProviderDocuments(string $body): array
    {
        $result = [];

        foreach ($this->getDocuments($body) as $document) {
            $result[] = [$document];
        }

        return $result;
    }

    /**
     * @param string $body
     * @return iterable|Document[]
     * @throws \League\Flysystem\FileNotFoundException
     * @throws \LogicException
     */
    protected function getDocuments(string $body): iterable
    {
        $readable = File::fromSources($body);

        foreach ($this->getCompilers() as $compiler) {
            yield $compiler->compile($readable);
        }
    }

    /**
     * @return LoggerInterface
     */
    private function getLogger(): LoggerInterface
    {
        return new Logger(\class_basename(static::class), [
            new StreamHandler(@\fopen('php://output', 'wb+')),
        ]);
    }

    /**
     * @return \Generator|Compiler[]
     * @throws \League\Flysystem\FileNotFoundException
     * @throws \LogicException
     */
    protected function getCompilers(): \Generator
    {
        // Default
        yield new Compiler(null);

        // Nullable (Return Document "as is")
        yield new Compiler(new NullablePersister());

        // Array (Return Document "as is" and store same files into php array stateless memory)
        yield new Compiler(new ArrayPersister());

        // Emulation of data saving
        yield new Compiler(new EmulatingPersister());

        // PSR-6 + Flysystem Serialization
        yield new Compiler($this->getPsr6FileSystemPersister());

        // PSR-16 + Filesystem Serialization
        yield new Compiler($this->getPsr16FileSystemPersister());
    }

    /**
     * @return Persister
     * @throws \League\Flysystem\FileNotFoundException
     * @throws \LogicException
     */
    private function getPsr6FileSystemPersister(): Persister
    {
        $cachePool = $this->createFilesystemPool('psr6');

        return new Psr6Persister($cachePool, function (ReadableInterface $readable, Document $document) {
            return new CacheItem($readable->getHash(), true, $document);
        });
    }

    /**
     * @param string $name
     * @return AbstractCachePool
     * @throws \League\Flysystem\FileNotFoundException
     * @throws \LogicException
     */
    private function createFilesystemPool(string $name): AbstractCachePool
    {
        $filesystem = new Filesystem(new Local(__DIR__ . '/.temp/'));
        $folder = \snake_case(\class_basename($this)) .
            '/' . $name .
            '/' . \date('m') .
            '/' . \date('s');

        return new FilesystemCachePool($filesystem, $folder);
    }

    /**
     * @return Persister
     * @throws \League\Flysystem\FileNotFoundException
     * @throws \LogicException
     */
    private function getPsr16FileSystemPersister(): Persister
    {
        return new Psr16Persister($this->createFilesystemPool('psr16'));
    }

    /**
     * @param string $file
     * @return string
     */
    public function resource(string $file): string
    {
        return __DIR__ . '/.resources/' . $this->resourcesPath . $file;
    }

    /**
     * @return array
     * @throws \InvalidArgumentException
     * @throws \LogicException
     */
    public function loadPositiveABTests(): array
    {
        $finder = (new Finder())
            ->files()
            ->in($this->specDirectory)
            ->name('+*.graphqls');

        return $this->formatProvider($finder->getIterator());
    }

    /**
     * @return array
     * @throws \InvalidArgumentException
     * @throws \LogicException
     */
    public function loadNegativeABTests(): array
    {
        $finder = (new Finder())
            ->files()
            ->in($this->specDirectory)
            ->name('-*.graphqls');

        return $this->formatProvider($finder->getIterator());
    }

    /**
     * @param \Traversable $files
     * @return array
     * @throws \Railt\Compiler\Exceptions\NotReadableException
     */
    private function formatProvider(\Traversable $files): array
    {
        $tests = [];

        foreach ($files as $test) {
            $tests[] = [File::fromSplFileInfo($test)];
        }

        return $tests;
    }

    /**
     * @param string $file
     * @return File
     * @throws \Railt\Compiler\Exceptions\NotReadableException
     */
    public function file(string $file): File
    {
        return File::fromPathname($this->resource($file));
    }
}
