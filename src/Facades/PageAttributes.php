<?php

namespace Fsmdev\LaravelPageAttributes\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class PageAttributes
 * @package Fsmdev\LaravelPageAttributes\Facades
 *
 * @method static void set(string|array $name, string $value)
 * @method static string|null get(string $name)
 * @method static string html(string $name)
 * @method  static void context(integer|null $context = null)
 */
class PageAttributes extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'fsmdev_laravel_page_attributes';
    }
}