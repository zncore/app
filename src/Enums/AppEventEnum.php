<?php

namespace ZnCore\App\Enums;

/**
 * Перечисление событий инициализации приложения
 */
class AppEventEnum
{

    public const BEFORE_INIT_ENV = 'app.beforeInitEnv';
    public const AFTER_INIT_ENV = 'app.afterInitEnv';

    public const BEFORE_INIT_CONTAINER = 'app.beforeInitContainer';
    public const AFTER_INIT_CONTAINER = 'app.afterInitContainer';

    public const BEFORE_INIT_BUNDLES = 'app.beforeInitBundles';
    public const AFTER_INIT_BUNDLES = 'app.afterInitBundles';

    public const BEFORE_INIT_DISPATCHER = 'app.beforeInitDispatcher';
    public const AFTER_INIT_DISPATCHER = 'app.afterInitDispatcher';

}
