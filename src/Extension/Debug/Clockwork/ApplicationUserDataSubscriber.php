<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Extension\Debug\Clockwork;

use Clockwork\Clockwork;
use Clockwork\Request\UserData;
use Illuminate\Support\Arr;
use Railt\Container\Container;
use Railt\Container\Exception\ContainerInvocationException;
use Railt\Container\Exception\ContainerResolutionException;
use Railt\Container\Exception\ParameterResolutionException;
use Railt\Dumper\TypeDumper;
use Railt\Foundation\Config\RepositoryInterface;
use Railt\Foundation\Event\Http\ResponseProceed;
use Railt\SDL\Reflection\Dictionary;
use Railt\SDL\Standard\StandardType;

/**
 * Class ApplicationUserDataSubscriber
 */
class ApplicationUserDataSubscriber extends UserDataSubscriber
{
    /**
     * @var Container
     */
    private $app;

    /**
     * @var UserData
     */
    private $data;

    /**
     * FieldResolveSubscriber constructor.
     *
     * @param Clockwork $clockwork
     * @param Container $app
     * @throws \ReflectionException
     */
    public function __construct(Clockwork $clockwork, Container $app)
    {
        $this->app = $app;
        $this->data = $clockwork->userData('railt')->title('Railt');

        $this->shareContainer();
        $this->shareConfigs();
    }

    /**
     * @throws \ReflectionException
     */
    private function shareContainer(): void
    {
        $this->data->table('Application Container', $this->getContainerTable($this->app));
    }

    /**
     * @throws ContainerInvocationException
     * @throws ContainerResolutionException
     * @throws ParameterResolutionException
     */
    private function shareConfigs(): void
    {
        /** @var RepositoryInterface $config */
        $config = $this->app->make(RepositoryInterface::class);

        $configs = [];

        foreach (Arr::dot($config->all()) as $key => $value) {
            $value = \is_scalar($value) ? $value : TypeDumper::render($value);

            $configs[] = ['Name' => $key, 'Value' => $value];
        }

        $this->data->table('Config', $configs);
    }

    /**
     * @param ResponseProceed $response
     */
    public function onResponse(ResponseProceed $response): void
    {
        $this->shareGraphQLTypes();
    }

    /**
     * @return void
     */
    private function shareGraphQLTypes(): void
    {
        $dictionary = $this->app->make(Dictionary::class);

        $types = [];

        foreach ($dictionary->all() as $type) {
            $std = $type instanceof StandardType;
            $isFile = $type->getDocument()->getFile()->exists();

            $types[] = [
                'Type'     => ($std ? '(builtin) ' : '') . (string)$type,
                'Document' => $isFile ? $type->getDocument()->getFile()->getPathname() : 'runtime',
            ];
        }

        $this->data->table('GraphQL SDL', $types);
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ResponseProceed::class => ['onResponse', -100],
        ];
    }
}
