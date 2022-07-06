<?php

namespace ZnCore\App\Interfaces;

/**
 * Интерфейс приложения.
 */
interface AppInterface
{

    /**
     * Имя приложения.
     * 
     * Например: console, web, admin
     * 
     * @return string
     */
    public function appName(): string;

    /**
     * Инициализация приложения
     */
    public function init(): void;
}
