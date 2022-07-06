<?php

namespace ZnCore\App\Libs;

use Psr\Container\ContainerInterface;
//use Symfony\Component\EventDispatcher\EventDispatcher;
//use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use ZnCore\App\Loaders\ConfigCollectionLoader;
use ZnCore\Base\ConfigManager\Interfaces\ConfigManagerInterface;
use ZnCore\Base\ConfigManager\Libs\ConfigManager;
use ZnCore\Container\Helpers\ContainerHelper;
use ZnCore\Container\Interfaces\ContainerConfiguratorInterface;
use ZnCore\Container\Libs\ContainerConfigurator;
use ZnCore\Container\Traits\ContainerAwareTrait;
//use ZnCore\EventDispatcher\Interfaces\EventDispatcherConfiguratorInterface;
//use ZnCore\EventDispatcher\Libs\EventDispatcherConfigurator;
use ZnCore\Contract\Common\Exceptions\ReadOnlyException;
//use ZnCore\EntityManager\Interfaces\EntityManagerConfiguratorInterface;
//use ZnCore\EntityManager\Interfaces\EntityManagerInterface;
//use ZnCore\EntityManager\Libs\EntityManager;
//use ZnCore\EntityManager\Libs\EntityManagerConfigurator;
use ZnLib\Components\I18Next\Facades\I18Next;

class ZnCore
{

    use ContainerAwareTrait;

    public function init(): void
    {
        $this->initContainer();
//        $this->initI18Next();
        $container = $this->getContainer();
        $this->configureContainer($container);
    }

    public function configureContainer(ContainerInterface $container) {
        $containerConfigurator = new ContainerConfigurator($container);

        $containerConfigurator->singleton(ContainerInterface::class, function () use($container) {
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

    /*private function initI18Next()
    {
        $container = $this->getContainer();
        try {
            I18Next::setContainer($container);
        } catch (ReadOnlyException $exception) {
        }
    }*/

    protected function configContainer(ContainerConfiguratorInterface $containerConfigurator): void
    {
        $containerConfigurator->singleton(ContainerConfiguratorInterface::class, function () use ($containerConfigurator) {
            return $containerConfigurator;
        });

        /*$containerConfigurator->singleton(EntityManagerInterface::class, function (ContainerInterface $container) {
            $em = EntityManager::getInstance($container);
//            $eloquentOrm = $container->get(EloquentOrm::class);
//            $em->addOrm($eloquentOrm);
            return $em;
        });

        $containerConfigurator->singleton(EntityManagerConfiguratorInterface::class, EntityManagerConfigurator::class);*/

        $entityManagerConfigCallback = require __DIR__ . '/../../../entity-manager/src/config/container.php';
        call_user_func($entityManagerConfigCallback, $containerConfigurator);

        $eventDispatcherConfigCallback = require __DIR__ . '/../../../event-dispatcher/src/config/container.php';
        call_user_func($eventDispatcherConfigCallback, $containerConfigurator);


//        $containerConfigurator->singleton(EventDispatcherConfiguratorInterface::class, EventDispatcherConfigurator::class);
//        $containerConfigurator->singleton(EventDispatcherInterface::class, EventDispatcher::class);
//        $containerConfigurator->singleton(\Psr\EventDispatcher\EventDispatcherInterface::class, EventDispatcherInterface::class);

        $containerConfigurator->singleton(ConfigManagerInterface::class, ConfigManager::class);
    }
}
