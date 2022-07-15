<?php

namespace ZnCore\App\Base;

use Psr\Container\ContainerInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use ZnCore\App\Enums\AppEventEnum;
use ZnCore\App\Interfaces\AppInterface;
use ZnCore\App\Libs\ZnCore;
use ZnCore\Arr\Helpers\ArrayHelper;
use ZnCore\Bundle\Libs\BundleLoader;
use ZnCore\Container\Interfaces\ContainerConfiguratorInterface;
use ZnCore\Container\Traits\ContainerAttributeTrait;
use ZnCore\DotEnv\Domain\Libs\DotEnv;
use ZnCore\Env\Helpers\EnvHelper;
use ZnCore\EventDispatcher\Interfaces\EventDispatcherConfiguratorInterface;
use ZnCore\EventDispatcher\Traits\EventDispatcherTrait;

/**
 * Абстрактный класс инициализатора приложения.
 *
 * Шаги инициализации:
 *
 *  - Инициализация окружения
 *  - Инициализация DI-контейнера
 *  - Загрузка бандлов
 *  - Инициализация диспетчера событий
 *
 */
abstract class BaseApp implements AppInterface
{

    use ContainerAttributeTrait;
    use EventDispatcherTrait;

    private $containerConfigurator;
    private $znCore;
    protected $bundles = [];
    private $import = [];
    private $bundleLoader;

    abstract public function appName(): string;

    public function setBundles(array $bundles)
    {
        $this->bundles = $bundles;
    }

    public function addBundles(array $bundles): void
    {
        $this->bundles = ArrayHelper::merge($this->bundles, $bundles);
    }

    public function import(): array
    {
        return $this->import;
    }

    protected function bundles(): array
    {
        return $this->bundles;
    }

    public function __construct(
        ContainerInterface $container,
        EventDispatcherInterface $dispatcher,
        ZnCore $znCore,
        ContainerConfiguratorInterface $containerConfigurator
    )
    {
        $this->setContainer($container);
        $this->setEventDispatcher($dispatcher);
        $this->containerConfigurator = $containerConfigurator;
        $this->znCore = $znCore;
    }

    /**
     * Инициализация приложения
     */
    public function init(): void
    {
        $this->dispatchEvent(AppEventEnum::BEFORE_INIT_ENV);
        $this->initEnv();
        $this->dispatchEvent(AppEventEnum::AFTER_INIT_ENV);

        $this->dispatchEvent(AppEventEnum::BEFORE_INIT_CONTAINER);
        $this->initContainer();
        $this->dispatchEvent(AppEventEnum::AFTER_INIT_CONTAINER);

        $this->dispatchEvent(AppEventEnum::BEFORE_INIT_BUNDLES);
        $this->initBundles();
        $this->dispatchEvent(AppEventEnum::AFTER_INIT_BUNDLES);

        $this->dispatchEvent(AppEventEnum::BEFORE_INIT_DISPATCHER);
        $this->initDispatcher();
        $this->dispatchEvent(AppEventEnum::AFTER_INIT_DISPATCHER);
    }

    /**
     * Инициализация окружения
     */
    protected function initEnv(): void
    {
        DotEnv::init($_ENV['APP_MODE']);
//        EnvHelper::prepareTestEnv();
//        DotEnv::init();
        EnvHelper::setErrorVisibleFromEnv();
    }

    /**
     * Инициализация DI-контейнера
     *
     * Объявляет только самые необходимые зависимости для запуска приложения.
     */
    protected function initContainer(): void
    {
        $this->configContainer($this->containerConfigurator);
    }

    /**
     * Загрузка подкюченных бандлов.
     */
    protected function initBundles(): void
    {
        $bundleLoader = $this->getBundleLoader();
        $bundleLoader->loadMainConfig($this->appName());
    }

    /**
     * Инициализация диспетчера событий.
     *
     *
     */
    protected function initDispatcher(): void
    {
        $eventDispatcherConfigurator = $this->getContainer()->get(EventDispatcherConfiguratorInterface::class);
        $this->configDispatcher($eventDispatcherConfigurator);
    }

    /**
     * Получить конфиг загрузчиков бандла
     * @return array
     */
    protected function bundleLoaders(): array
    {
        return include __DIR__ . '/../../../../znlib/components/src/DefaultApp/config/bundleLoaders.php';
    }

    /**
     * Создать загрузчик бандла
     * @return BundleLoader
     */
    protected function createBundleLoaderInstance(): BundleLoader
    {
        return new BundleLoader($this->bundles(), $this->import());
    }

    /**
     * Конфигурироывть загрузчик бандла
     * @param BundleLoader $bundleLoader
     */
    protected function configureBundleLoader(BundleLoader $bundleLoader): void
    {
        $loaders = $this->bundleLoaders();
        if ($loaders) {
            foreach ($loaders as $loaderName => $loaderDefinition) {
                $bundleLoader->registerLoader($loaderName, $loaderDefinition);
            }
        }
    }

    /**
     * Получить объект загрузчика бандла
     * @return BundleLoader
     */
    protected function getBundleLoader(): BundleLoader
    {
        if ($this->bundleLoader == null) {
            $this->bundleLoader = $this->createBundleLoaderInstance();
            $this->configureBundleLoader($this->bundleLoader);
        }
        return $this->bundleLoader;
    }

    /**
     * Конфигурация диспетчера событий
     *
     * @param EventDispatcherConfiguratorInterface $configurator
     */
    protected function configDispatcher(EventDispatcherConfiguratorInterface $configurator): void
    {

    }

    /**
     * Опубликовать событие
     *
     * @param string $eventName
     */
    protected function dispatchEvent(string $eventName): void
    {
        $event = new Event();
        $this->getEventDispatcher()->dispatch($event, $eventName);
    }
}
