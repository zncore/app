<?php

namespace ZnCore\App\Libs;

use Psr\Container\ContainerInterface;
use ZnCore\ConfigManager\Interfaces\ConfigManagerInterface;
use ZnCore\ConfigManager\Libs\ConfigManager;
use ZnCore\Container\Helpers\ContainerHelper;
use ZnCore\Container\Interfaces\ContainerConfiguratorInterface;
use ZnCore\Container\Libs\ContainerConfigurator;
use ZnCore\Container\Traits\ContainerAwareTrait;
use ZnCore\Contract\Common\Exceptions\ReadOnlyException;

/**
 * Инициализатор окружения и предварительных конфигов
 */
class ZnCore
{

    use ContainerAwareTrait;

    /**
     * Инициализация и конфигурация DI-контейнера
     */
    public function init(): void
    {
        $this->initContainer();
        $container = $this->getContainer();
        $this->configureContainer($container);
    }

    /**
     * Конфигурация DI-контейнера
     *
     * @param ContainerInterface $container
     */
    public function configureContainer(ContainerInterface $container)
    {
        $containerConfigurator = new ContainerConfigurator($container);
        $containerConfigurator->singleton(ContainerInterface::class, function () use ($container) {
            return $container;
        });
        $this->configContainer($containerConfigurator);
    }

    private function initContainer()
    {
        $container = $this->getContainer();
        try {
            ContainerHelper::setContainer($container);
        } catch (ReadOnlyException $exception) {
        }
    }

    /**
     * - Конфигуратор DI-контейнера
     * - Менеджер сущностей
     * - Диспетчер событий
     * - Менеджер конфигов бандлов
     *
     * @param ContainerConfiguratorInterface $containerConfigurator
     */
    protected function configContainer(ContainerConfiguratorInterface $containerConfigurator): void
    {
        $containerConfigurator->singleton(ContainerConfiguratorInterface::class, function () use ($containerConfigurator) {
            return $containerConfigurator;
        });

        $entityManagerConfigCallback = require __DIR__ . '/../../../../zndomain/entity-manager/src/config/container.php';
        call_user_func($entityManagerConfigCallback, $containerConfigurator);

        $eventDispatcherConfigCallback = require __DIR__ . '/../../../event-dispatcher/src/config/container.php';
        call_user_func($eventDispatcherConfigCallback, $containerConfigurator);

        $containerConfigurator->singleton(ConfigManagerInterface::class, ConfigManager::class);
    }
}
